<?php

namespace Webkul\Shop\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $productData = new ProductResource($this->product);
        $product = $productData->toArray($request);

        // For bundle products, try to use the downloadable child product image
        if ($this->product->type === 'bundle' && isset($product['bundle_options'])) {
            $downloadableImage = $this->getDownloadableChildImage($product);
            if ($downloadableImage) {
                $product['base_image'] = $downloadableImage;
            }
        }

        // For bundle products, recalculate min_price from bundle_format_prices
        if ($this->product->type === 'bundle' && isset($product['bundle_format_prices']) && !empty($product['bundle_format_prices'])) {
            $product['min_price'] = $this->getMinPriceFromBundleFormatPrices($product['bundle_format_prices']);
        }

        return [
            'id'      => $this->id,
            'options' => $this->resource->additional ?? [],
            'product' => $product,
        ];
    }

    /**
     * Get the downloadable child product image from bundle options
     *
     * @param  array  $product
     * @return array|null
     */
    private function getDownloadableChildImage($product)
    {
        if (!isset($product['bundle_options']) || !is_array($product['bundle_options'])) {
            return null;
        }

        // Get bundle options with products
        $bundleOptions = $this->product->bundle_options()->with('bundle_option_products.product')->get();

        foreach ($bundleOptions as $option) {
            foreach ($option->bundle_option_products as $optionProduct) {
                if ($optionProduct->product && $optionProduct->product->type === 'downloadable') {
                    $childImage = product_image()->getProductBaseImage($optionProduct->product);
                    if ($childImage) {
                        return $childImage;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get the minimum price from bundle_format_prices, excluding disabled child products
     *
     * @param  array  $bundleFormatPrices
     * @return string
     */
    private function getMinPriceFromBundleFormatPrices($bundleFormatPrices)
    {
        if (empty($bundleFormatPrices)) {
            return $bundleFormatPrices;
        }

        // Get bundle options with active child products
        $bundleOptions = $this->product->bundle_options()->with('bundle_option_products.product')->get();
        
        $minPrice = null;
        $minFormattedPrice = null;

        foreach ($bundleOptions as $option) {
            foreach ($option->bundle_option_products as $optionProduct) {
                $product = $optionProduct->product;
                
                // Skip disabled products (status = 0 or null)
                if (!$product || !$product->status) {
                    continue;
                }
                
                if ($product->format) {
                    // Get format name
                    $formatOption = app('Webkul\Attribute\Repositories\AttributeOptionRepository')
                        ->findOneByField('id', $product->format);
                    
                    if ($formatOption) {
                        $formatName = $formatOption->admin_name;
                        
                        // Check if this format price exists in bundleFormatPrices
                        if (isset($bundleFormatPrices[$formatName])) {
                            $formattedPrice = $bundleFormatPrices[$formatName];
                            
                            // Extract numeric value from formatted price string (e.g., "10.00 CHF" -> 10.00)
                            preg_match('/[\d,]+(?:\.\d{2})?/', str_replace(',', '', $formattedPrice), $matches);
                            
                            if (isset($matches[0])) {
                                $price = (float) str_replace(',', '', $matches[0]);
                                
                                if ($minPrice === null || $price < $minPrice) {
                                    $minPrice = $price;
                                    $minFormattedPrice = $formattedPrice;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $minFormattedPrice ?? reset($bundleFormatPrices);
    }
}
