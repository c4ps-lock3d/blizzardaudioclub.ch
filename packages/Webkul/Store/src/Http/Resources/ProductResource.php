<?php

namespace Webkul\Store\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Product\Helpers\Review;
use Webkul\Product\Models\ProductAttributeValue;

class ProductResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        $this->reviewHelper = app(Review::class);

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $productTypeInstance = $this->getTypeInstance();
        $formatName = app('Webkul\Attribute\Repositories\AttributeOptionRepository')
        ->findOneByField('id', $this->format);

        // Get downloadable child image for bundle products only if bundle has no image
        $bundleDownloadableImage = null;
        $bundleSimpleImage = null;
        if ($this->type === 'bundle') {
            // Check if bundle actually has images
            $hasImages = $this->images()->count() > 0;
            
            // Only get child images if the bundle doesn't have any images
            if (!$hasImages) {
                $bundleDownloadableImage = app('Webkul\Product\Helpers\BundleOption')->getBundleDownloadableImage($this);
                $bundleSimpleImage = app('Webkul\Product\Helpers\BundleOption')->getBundleSimpleImage($this);
            }
        }

        // Get bundle format prices
        $bundleFormatPrices = null;
        if ($this->type === 'bundle') {
            $bundleFormatPrices = $this->getBundleFormatPrices();
        }

        // Get bundle options with products for preorder detection
        $bundleOptions = null;
        if ($this->type === 'bundle') {
            $bundleOptions = $this->getBundleOptions();
        }

        return [
            'id'          => $this->id,
            'sku'         => $this->sku,
            'name'        => $this->name,
            'type'        => $this->type,
            'description' => $this->description,
            'price'       => $this->price,
            'release_date'=> $this->release_date,
            'format'      => $formatName ? $formatName->admin_name : $this->format,
            'qty'         => $this->inventories->sum('qty'),
            'url_key'     => $this->url_key,
            'base_image'  => product_image()->getProductBaseImage($this),
            'images'      => product_image()->getGalleryImages($this),
            'is_new'      => (bool) $this->new,
            'is_featured' => (bool) $this->featured,
            'preorder'    => (bool) $this->preorder,
            'on_sale'     => (bool) $productTypeInstance->haveDiscount(),
            'is_saleable' => (bool) $productTypeInstance->isSaleable(),
            'is_wishlist' => (bool) auth()->guard()->user()?->wishlist_items
                ->where('channel_id', core()->getCurrentChannel()->id)
                ->where('product_id', $this->id)->count(),
            'min_price'   => core()->formatPrice($productTypeInstance->getMinimalPrice()),
            'prices'      => $productTypeInstance->getProductPrices(),
            'price_html'  => $productTypeInstance->getPriceHtml(),
            'ratings'     => [
                'average' => $this->reviewHelper->getAverageRating($this),
                'total'   => $this->reviewHelper->getTotalRating($this),
            ],
            'bundle_downloadable_image' => $bundleDownloadableImage,
            'bundle_simple_image'       => $bundleSimpleImage,
            'bundle_format_prices'      => $bundleFormatPrices,
            'bundle_options'            => $bundleOptions,
        ];
    }

    /**
     * Get bundle products prices grouped by format
     */
    private function getBundleFormatPrices()
    {
        $formatPrices = [];
        
        $bundleProducts = $this->bundle_options()->with('bundle_option_products.product')->get();
        
        foreach ($bundleProducts as $bundleOption) {
            foreach ($bundleOption->bundle_option_products as $optionProduct) {
                $product = $optionProduct->product;
                
                if (!$product || !$product->format) continue;
                
                // Get format name using the product's format ID
                $formatOption = app('Webkul\Attribute\Repositories\AttributeOptionRepository')
                    ->findOneByField('id', $product->format);
                
                if ($formatOption) {
                    $price = $product->price;
                    $formattedPrice = core()->formatPrice($price);
                    $formatPrices[$formatOption->admin_name] = $formattedPrice;
                }
            }
        }
        
        return !empty($formatPrices) ? $formatPrices : null;
    }

    /**
     * Get bundle options with child products for preorder detection
     */
    private function getBundleOptions()
    {
        $options = [];
        
        $bundleOptions = $this->bundle_options()->with('bundle_option_products.product')->get();
        
        foreach ($bundleOptions as $option) {
            $products = [];
            
            foreach ($option->bundle_option_products as $optionProduct) {
                $product = $optionProduct->product;
                
                if (!$product) continue;
                
                $products[] = [
                    'id' => $product->id,
                    'type' => $product->type,
                    'preorder' => (bool) $product->preorder,
                ];
            }
            
            $options[] = [
                'id' => $option->id,
                'products' => $products,
            ];
        }
        
        return !empty($options) ? $options : null;
    }
}


