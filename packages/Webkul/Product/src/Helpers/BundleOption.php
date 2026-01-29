<?php

namespace Webkul\Product\Helpers;

class BundleOption
{
    /**
     * Product
     *
     * @var \Webkul\Product\Contracts\Product
     */
    protected $product;

    /**
     * Returns bundle option config
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array
     */
    public function getBundleConfig($product)
    {
        $this->product = $product;

        return [
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Get the image from downloadable child product
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array|null
     */
    public function getBundleDownloadableImage($product)
    {
        $this->product = $product;

        try {
            // eager load images for bundle products if not already loaded
            if (! $this->product->relationLoaded('bundle_options')) {
                $this->product->load('bundle_options.bundle_option_products.product.images');
            } else {
                $this->product->bundle_options->load('bundle_option_products.product.images');
            }

            // Find downloadable product and return its image
            foreach ($this->product->bundle_options as $option) {
                foreach ($option->bundle_option_products as $bundleOptionProduct) {
                    if ($bundleOptionProduct->product->type === 'downloadable' && $bundleOptionProduct->product->images->count() > 0) {
                        $image = $bundleOptionProduct->product->images->first();
                        return [
                            'original_image_url' => $image->url,
                            'large_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['large_image_url'] ?? $image->url,
                            'medium_image_url'   => product_image()->getProductBaseImage($bundleOptionProduct->product)['medium_image_url'] ?? $image->url,
                            'small_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['small_image_url'] ?? $image->url,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // If any error occurs, return null and fall back to product base image
            return null;
        }

        return null;
    }

    /**
     * Get the image from simple child product (vinyl/physical product)
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array|null
     */
    public function getBundleSimpleImage($product)
    {
        $this->product = $product;

        try {
            // eager load images for bundle products if not already loaded
            if (! $this->product->relationLoaded('bundle_options')) {
                $this->product->load('bundle_options.bundle_option_products.product.images');
            } else {
                $this->product->bundle_options->load('bundle_option_products.product.images');
            }

            // Find simple product and return its image
            foreach ($this->product->bundle_options as $option) {
                foreach ($option->bundle_option_products as $bundleOptionProduct) {
                    if ($bundleOptionProduct->product->type === 'simple' && $bundleOptionProduct->product->images->count() > 0) {
                        $image = $bundleOptionProduct->product->images->first();
                        return [
                            'original_image_url' => $image->url,
                            'large_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['large_image_url'] ?? $image->url,
                            'medium_image_url'   => product_image()->getProductBaseImage($bundleOptionProduct->product)['medium_image_url'] ?? $image->url,
                            'small_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['small_image_url'] ?? $image->url,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // If any error occurs, return null and fall back to product base image
            return null;
        }

        return null;
    }

    /**
     * Returns bundle options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        // eager load all inventories for bundle options
        $this->product->bundle_options->load('bundle_option_products.product.inventories');

        foreach ($this->product->bundle_options as $option) {
            $data = $this->getOptionItemData($option);

            if (
                ! $option->is_required
                && ! count($data['products'])
            ) {
                continue;
            }

            $options[$option->id] = $data;
        }

        usort($options, function ($a, $b) {
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            }

            return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
        });

        return $options;
    }

    /**
     * Get formed data from bundle option
     *
     * @param  \Product\Product\Contracts\ProductBundleOption  $option
     * @return array
     */
    private function getOptionItemData($option)
    {
        return [
            'id'          => $option->id,
            'label'       => $option->label,
            'type'        => $option->type,
            'is_required' => $option->is_required,
            'products'    => $this->getOptionProducts($option),
            'sort_order'  => $option->sort_order,
        ];
    }

    /**
     * Get formed data from bundle option product
     *
     * @param  \Product\Product\Contracts\ProductBundleOption  $option
     * @return array
     */
    private function getOptionProducts($option)
    {
        $products = [];

        foreach ($option->bundle_option_products as $index => $bundleOptionProduct) {
            // For simple products in bundles, always include them as they are options
            // For other types, skip if not saleable
            $isSimple = $bundleOptionProduct->product->type === 'simple';
            $isSaleable = $bundleOptionProduct->product->getTypeInstance()->isSaleable();
            
            if (!$isSimple && !$isSaleable) {
                continue;
            }

            $formatName = null;
            if ($bundleOptionProduct->product->format) {
                $formatOption = app('Webkul\Attribute\Repositories\AttributeOptionRepository')
                    ->findOneByField('id', $bundleOptionProduct->product->format);
                $formatName = $formatOption ? $formatOption->label : null;
            }

            // Get product images
            $images = [];
            foreach ($bundleOptionProduct->product->images as $image) {
                $images[] = [
                    'original_image_url' => $image->url,
                    'large_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['large_image_url'] ?? $image->url,
                    'medium_image_url'   => product_image()->getProductBaseImage($bundleOptionProduct->product)['medium_image_url'] ?? $image->url,
                    'small_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['small_image_url'] ?? $image->url,
                    'type'               => 'images',
                    'product_format'     => $formatName,
                    'product_name'       => $bundleOptionProduct->product->name,
                ];
            }

            $products[$bundleOptionProduct->id] = [
                'id'         => $bundleOptionProduct->id,
                'qty'        => $bundleOptionProduct->qty,
                'price'      => $bundleOptionProduct->product->getTypeInstance()->getProductPrices(),
                'name'       => $bundleOptionProduct->product->name,
                'format'     => $formatName,
                'product_id' => $bundleOptionProduct->product_id,
                'type'       => $bundleOptionProduct->product->type,
                'is_default' => $bundleOptionProduct->is_default,
                'sort_order' => $bundleOptionProduct->sort_order,
                'in_stock'   => $bundleOptionProduct->product->inventories->sum('qty') >= $bundleOptionProduct->qty,
                'inventory'  => $bundleOptionProduct->product->inventories->sum('qty'),
                'images'     => $images,
            ];
        }

        usort($products, function ($a, $b) {
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            }

            return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
        });

        return $products;
    }

    /**
     * Get all images from bundle child products
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return array
     */
    public function getBundleChildImages($product)
    {
        $this->product = $product;
        $images = [];

        // eager load images for all bundle products
        $this->product->bundle_options->load('bundle_option_products.product.images');

        foreach ($this->product->bundle_options as $option) {
            foreach ($option->bundle_option_products as $bundleOptionProduct) {
                // For simple products in bundles, always include them as they are options
                // For other types, skip if not saleable
                $isSimple = $bundleOptionProduct->product->type === 'simple';
                $isSaleable = $bundleOptionProduct->product->getTypeInstance()->isSaleable();
                
                if (!$isSimple && !$isSaleable) {
                    continue;
                }

                $formatName = null;
                if ($bundleOptionProduct->product->format) {
                    $formatOption = app('Webkul\Attribute\Repositories\AttributeOptionRepository')
                        ->findOneByField('id', $bundleOptionProduct->product->format);
                    $formatName = $formatOption ? $formatOption->label : null;
                }

                foreach ($bundleOptionProduct->product->images as $image) {
                    $images[] = [
                        'original_image_url' => $image->url,
                        'large_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['large_image_url'] ?? $image->url,
                        'medium_image_url'   => product_image()->getProductBaseImage($bundleOptionProduct->product)['medium_image_url'] ?? $image->url,
                        'small_image_url'    => product_image()->getProductBaseImage($bundleOptionProduct->product)['small_image_url'] ?? $image->url,
                        'type'               => 'images',
                        'product_format'     => $formatName,
                        'product_name'       => $bundleOptionProduct->product->name,
                    ];
                }
            }
        }

        return $images;
    }
}
