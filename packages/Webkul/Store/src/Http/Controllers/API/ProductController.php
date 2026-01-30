<?php

namespace Webkul\Store\Http\Controllers\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Marketing\Jobs\UpdateCreateSearchTerm as UpdateCreateSearchTermJob;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Store\Http\Resources\ProductResource;
use Webkul\Product\Models\Product;


class ProductController extends APIController
{
    /**
     * Create a controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Product listings.
     */
    public function index(): JsonResource
    {
        if (core()->getConfigData('catalog.products.search.engine') == 'elastic') {
            $searchEngine = core()->getConfigData('catalog.products.search.storefront_mode');
        }

        $products = $this->productRepository
            ->setSearchEngine($searchEngine ?? 'database')
            ->getAll(array_merge(request()->query(), [
                'channel_id'           => core()->getCurrentChannel()->id,
                'status'               => 1,
                'visible_individually' => 1,
            ]));

        // If filters are applied (and not just pagination/sorting), add bundles with matching children
        $filterParams = request()->query();
        $skipParams = ['mode', 'sort', 'limit', 'page', 'query'];
        $hasFilters = count(array_diff_key($filterParams, array_flip($skipParams))) > 0;

        if ($hasFilters) {
            // Get matching bundles that should be included
            $matchingBundles = $this->getMatchingBundles();
            
            // Also get matching simple products that might not be returned by the repository
            // (e.g., products where format is in the name, not as an attribute)
            $matchingSimpleProducts = $this->getMatchingSimpleProducts();
            
            $allMatching = $matchingBundles->merge($matchingSimpleProducts);
            
            if ($allMatching->isNotEmpty()) {
                // Add products to the existing collection, avoiding duplicates
                $currentCollection = $products->getCollection();
                $existingIds = $currentCollection->pluck('id')->toArray();
                $addedCount = 0;
                
                foreach ($allMatching as $product) {
                    if (!in_array($product->id, $existingIds)) {
                        $currentCollection->push($product);
                        $addedCount++;
                    }
                }
                
                // Update pagination total to reflect added products
                // We need to use reflection to update the private total property
                if ($addedCount > 0) {
                    $reflection = new \ReflectionClass($products);
                    $property = $reflection->getProperty('total');
                    $property->setAccessible(true);
                    $property->setValue($products, $products->total() + $addedCount);
                }
            }
        }

        if (! empty(request()->query('query'))) {
            /**
             * Update or create search term only if
             * there is only one filter that is query param
             */
            if (count(request()->except(['mode', 'sort', 'limit'])) == 1) {
                UpdateCreateSearchTermJob::dispatch([
                    'term'       => request()->query('query'),
                    'results'    => $products->total(),
                    'channel_id' => core()->getCurrentChannel()->id,
                    'locale'     => app()->getLocale(),
                ]);
            }
        }

        return ProductResource::collection($products);
    }

    /**
     * Get matching bundles that should be included
     */
    private function getMatchingBundles()
    {
        $filterParams = request()->query();
        $skipParams = ['mode', 'sort', 'limit', 'category_id', 'channel_id', 'page', 'query', 'price'];
        $filterAttributes = array_diff_key($filterParams, array_flip($skipParams));
        
        $categoryId = $filterParams['category_id'] ?? null;

        // Query bundles in the same category using product_flats for status
        $bundleQuery = Product::where('type', 'bundle')
            ->whereHas('product_flats', function ($q) {
                $q->where('status', 1)
                  ->where('visible_individually', 1);
            });

        if ($categoryId) {
            $bundleQuery->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('product_categories.category_id', $categoryId);
            });
        }

        // If no filters, return all bundles
        if (empty($filterAttributes)) {
            return $bundleQuery->get();
        }

        // Convert attribute option IDs to their text values
        $filterAttributes = $this->resolveFilterAttributeValues($filterAttributes);

        // Filter bundles: bundle matches if parent has attribute OR at least one child has attribute
        $matchingBundles = collect();
        foreach ($bundleQuery->get() as $bundle) {
            if ($this->bundleMatchesFilter($bundle, $filterAttributes)) {
                $matchingBundles->push($bundle);
            }
        }

        return $matchingBundles;
    }

    /**
     * Resolve filter attribute values from option IDs to actual values.
     * E.g., convert "10" (option ID) to "vinyle" (option label)
     */
    private function resolveFilterAttributeValues(array $filterAttributes): array
    {
        $resolved = [];

        foreach ($filterAttributes as $attributeCode => $attributeValues) {
            $values = is_array($attributeValues) ? $attributeValues : array_filter(explode(',', $attributeValues));
            
            if (empty($values)) {
                continue;
            }

            // Try to get the attribute to check if it's a SELECT type
            $attribute = \Webkul\Attribute\Models\AttributeProxy::modelClass()::where('code', $attributeCode)->first();
            
            if ($attribute && $attribute->type === 'select') {
                // Convert option IDs to labels
                $resolvedValues = [];
                foreach ($values as $value) {
                    // Check if value is numeric (option ID)
                    if (is_numeric($value)) {
                        $option = $attribute->options()->find($value);
                        if ($option) {
                            $resolvedValues[] = $option->admin_name ?? $value;
                        } else {
                            $resolvedValues[] = $value;
                        }
                    } else {
                        $resolvedValues[] = $value;
                    }
                }
                $resolved[$attributeCode] = $resolvedValues;
            } else {
                // For non-select attributes, keep as is
                $resolved[$attributeCode] = $values;
            }
        }

        return $resolved;
    }

    /**
     * Get simple/downloadable products that match the current filters.
     * This includes products where the filter attribute might be in the product name.
     */
    private function getMatchingSimpleProducts()
    {
        $filterParams = request()->query();
        $skipParams = ['mode', 'sort', 'limit', 'category_id', 'channel_id', 'page', 'query', 'price'];
        $filterAttributes = array_diff_key($filterParams, array_flip($skipParams));
        
        $categoryId = $filterParams['category_id'] ?? null;

        // Query simple/downloadable products (not bundles)
        $productQuery = Product::whereNotIn('type', ['bundle', 'configurable'])
            ->whereHas('product_flats', function ($q) {
                $q->where('status', 1)
                  ->where('visible_individually', 1);
            });

        if ($categoryId) {
            $productQuery->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('product_categories.category_id', $categoryId);
            });
        }

        // If no filters, return empty (let repository handle it)
        if (empty($filterAttributes)) {
            return collect();
        }

        // Convert attribute option IDs to their text values
        $filterAttributes = $this->resolveFilterAttributeValues($filterAttributes);

        // Filter products: product matches if it has the filter attribute
        $matchingProducts = collect();
        foreach ($productQuery->get() as $product) {
            if ($this->productMatchesFilter($product, $filterAttributes)) {
                $matchingProducts->push($product);
            }
        }

        return $matchingProducts;
    }

    /**
     * Check if a simple product matches the filter criteria.
     */
    private function productMatchesFilter($product, array $filterAttributes): bool
    {
        foreach ($filterAttributes as $attributeCode => $attributeValue) {
            // Normaliser les valeurs
            $values = is_array($attributeValue) ? $attributeValue : array_filter(explode(',', $attributeValue));
            
            if (empty($values)) {
                continue;
            }
            
            // Get the attribute to check its type
            $attribute = \Webkul\Attribute\Models\AttributeProxy::modelClass()::where('code', $attributeCode)->first();
            
            if ($attribute && $attribute->type === 'select') {
                // For select attributes, check integer_value (option ID)
                // First convert label values to option IDs
                $optionIds = [];
                foreach ($values as $value) {
                    if (is_numeric($value)) {
                        $optionIds[] = $value;
                    } else {
                        // It's a label, find the option ID
                        $option = $attribute->options()->where('admin_name', $value)->first();
                        if ($option) {
                            $optionIds[] = $option->id;
                        }
                    }
                }
                
                if (!empty($optionIds)) {
                    $hasAttribute = $product->attribute_values()
                        ->whereHas('attribute', function ($q) use ($attributeCode) {
                            $q->where('code', $attributeCode);
                        })
                        ->whereIn('integer_value', $optionIds)
                        ->exists();
                    
                    if ($hasAttribute) {
                        return true; // Product matches
                    }
                }
            } else {
                // For text attributes, check text_value
                $hasAttribute = $product->attribute_values()
                    ->whereHas('attribute', function ($q) use ($attributeCode) {
                        $q->where('code', $attributeCode);
                    })
                    ->whereIn('text_value', $values)
                    ->exists();
                
                if ($hasAttribute) {
                    return true; // Product matches
                }
            }
            
            // For format attribute, also check product name (fallback only if no attribute exists)
            if ($attributeCode === 'format') {
                $hasFormatAttribute = $product->attribute_values()
                    ->whereHas('attribute', function ($q) {
                        $q->where('code', 'format');
                    })
                    ->exists();
                
                // Only use name fallback if there's NO format attribute at all
                if (!$hasFormatAttribute) {
                    if (preg_match('/\((vinyle|digital|cd|mp3)\)/i', $product->name, $matches)) {
                        $extractedFormat = strtolower($matches[1]);
                        if (in_array($extractedFormat, $values)) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false; // Product doesn't match any filter
    }

    /**
     * Check if a bundle matches the filter criteria.
     * Returns true if parent OR any child has the filter attribute.
     */
    private function bundleMatchesFilter($bundle, array $filterAttributes): bool
    {
        foreach ($filterAttributes as $attributeCode => $attributeValue) {
            // Normaliser les valeurs
            $values = is_array($attributeValue) ? $attributeValue : array_filter(explode(',', $attributeValue));
            
            if (empty($values)) {
                continue;
            }
            
            // Get the attribute to check its type
            $attribute = \Webkul\Attribute\Models\AttributeProxy::modelClass()::where('code', $attributeCode)->first();
            
            // Check if parent bundle has this attribute with the matching value
            if ($attribute && $attribute->type === 'select') {
                // For select attributes, check integer_value (option ID)
                $optionIds = [];
                foreach ($values as $value) {
                    if (is_numeric($value)) {
                        $optionIds[] = $value;
                    } else {
                        $option = $attribute->options()->where('admin_name', $value)->first();
                        if ($option) {
                            $optionIds[] = $option->id;
                        }
                    }
                }
                
                if (!empty($optionIds)) {
                    $parentMatches = $bundle->attribute_values()
                        ->whereHas('attribute', function ($q) use ($attributeCode) {
                            $q->where('code', $attributeCode);
                        })
                        ->whereIn('integer_value', $optionIds)
                        ->exists();
                    
                    if ($parentMatches) {
                        return true; // Parent matches
                    }
                }
            } else {
                // For text attributes, check text_value
                $parentMatches = $bundle->attribute_values()
                    ->whereHas('attribute', function ($q) use ($attributeCode) {
                        $q->where('code', $attributeCode);
                    })
                    ->whereIn('text_value', $values)
                    ->exists();
                
                if ($parentMatches) {
                    return true; // Parent matches
                }
            }
            
            // Check if ANY child has this attribute with the matching value
            $childMatches = false;
            
            // Get bundle options for this product
            $bundleOptions = $bundle->bundle_options;
            
            if ($bundleOptions) {
                foreach ($bundleOptions as $option) {
                    // Get bundle option products (children)
                    $children = $option->bundle_option_products;
                    
                    if ($children) {
                        foreach ($children as $child) {
                            if ($child->product) {
                                $childProduct = $child->product;
                                
                                // First check actual attribute values (source of truth)
                                if ($attribute && $attribute->type === 'select') {
                                    $optionIds = [];
                                    foreach ($values as $value) {
                                        if (is_numeric($value)) {
                                            $optionIds[] = $value;
                                        } else {
                                            $option = $attribute->options()->where('admin_name', $value)->first();
                                            if ($option) {
                                                $optionIds[] = $option->id;
                                            }
                                        }
                                    }
                                    
                                    if (!empty($optionIds)) {
                                        $hasMatchingAttribute = $childProduct->attribute_values()
                                            ->whereHas('attribute', function ($q) use ($attributeCode) {
                                                $q->where('code', $attributeCode);
                                            })
                                            ->whereIn('integer_value', $optionIds)
                                            ->exists();
                                        
                                        if ($hasMatchingAttribute) {
                                            $childMatches = true;
                                            break;
                                        }
                                    }
                                } else {
                                    $hasMatchingAttribute = $childProduct->attribute_values()
                                        ->whereHas('attribute', function ($q) use ($attributeCode) {
                                            $q->where('code', $attributeCode);
                                        })
                                        ->whereIn('text_value', $values)
                                        ->exists();
                                    
                                    if ($hasMatchingAttribute) {
                                        $childMatches = true;
                                        break;
                                    }
                                }
                                
                                // If no attribute, check product name (fallback)
                                if ($attributeCode === 'format') {
                                    $hasFormatAttribute = $childProduct->attribute_values()
                                        ->whereHas('attribute', function ($q) {
                                            $q->where('code', 'format');
                                        })
                                        ->exists();
                                    
                                    // Only use name fallback if there's NO format attribute at all
                                    if (!$hasFormatAttribute) {
                                        // Extract format from product name like "(vinyle)" or "(digital)"
                                        if (preg_match('/\((vinyle|digital|cd|mp3)\)/i', $childProduct->name, $matches)) {
                                            $extractedFormat = strtolower($matches[1]);
                                            if (in_array($extractedFormat, $values)) {
                                                $childMatches = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($childMatches) {
                        break;
                    }
                }
            }
            
            if ($childMatches) {
                return true; // At least one child matches
            }
            
            // If we reach here, this attribute doesn't match for parent or children
            // So bundle doesn't match all filters
            return false;
        }
        
        return false; // No matching filters found
    }

    /**
     * Related product listings.
     *
     * @param  int  $id
     */
    public function relatedProducts($id): JsonResource
    {
        $product = $this->productRepository->findOrFail($id);

        $relatedProducts = $product->related_products()
            ->take(core()->getConfigData('catalog.products.product_view_page.no_of_related_products'))
            ->get();

        return ProductResource::collection($relatedProducts);
    }

    /**
     * Up-sell product listings.
     *
     * @param  int  $id
     */
    public function upSellProducts($id): JsonResource
    {
        $product = $this->productRepository->findOrFail($id);

        $upSellProducts = $product->up_sells()
            ->take(core()->getConfigData('catalog.products.product_view_page.no_of_up_sells_products'))
            ->get();

        return ProductResource::collection($upSellProducts);
    }

    /**
     * Get bundles that have children matching the current filters.
     * This is an alternative endpoint - the main logic is now in index().
     */
    public function bundlesWithMatchingChildren(): JsonResource
    {
        $matchingBundles = $this->getMatchingBundles();

        return ProductResource::collection($matchingBundles);
    }

}
