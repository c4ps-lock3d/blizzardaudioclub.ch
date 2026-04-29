<?php

namespace Webkul\Store\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Category\Models\Category;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // Filter options based on attribute code and category
        $options = $this->options;
        
        if ($this->code === 'format') {
            $categoryId = request('category_id');
            
            $options = $this->options->filter(function ($option) use ($categoryId) {
                $optionName = strtolower($option->admin_name ?? $option->label);
                
                // Always exclude "bundle"
                if ($optionName === 'bundle') {
                    return false;
                }
                
                // Handle specific categories
                if ($categoryId) {
                    $category = Category::find($categoryId);
                    if ($category) {
                        $categoryName = strtolower($category->name);
                        
                        // If category is Merchandising, exclude "digital"
                        if ($categoryName === 'merchandising') {
                            if ($optionName === 'digital') {
                                return false;
                            }
                        }
                        
                        // If category is Musique, keep only: vinyle, cd, cassette, digital
                        if ($categoryName === 'musique') {
                            $allowedFormats = ['vinyle', 'cd', 'cassette', 'digital'];
                            if (!in_array($optionName, $allowedFormats)) {
                                return false;
                            }
                        }
                    }
                }
                
                return true;
            });
        }

        return [
            'id'      => $this->id,
            'code'    => $this->code,
            'type'    => $this->type,
            'name'    => $this->name ?? $this->admin_name,
            'options' => AttributeOptionResource::collection($options),
        ];
    }
}
