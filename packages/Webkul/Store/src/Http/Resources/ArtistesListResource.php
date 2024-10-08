<?php

namespace Webkul\Store\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtistesListResource extends JsonResource
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
            'id'                => $this->id,
            'slug'                => $this->slug,
            'name'              => $this->name,
            'image'              => $this->image,
        ];
    }
}