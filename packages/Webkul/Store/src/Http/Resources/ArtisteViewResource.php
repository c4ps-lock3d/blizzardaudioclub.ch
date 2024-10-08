<?php

namespace Webkul\Store\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Store\Http\Resources\ProductResource;

class ArtisteViewResource extends JsonResource
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'content' => $this->content,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}