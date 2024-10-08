<?php

namespace Webkul\Store\Http\Controllers\API;

use App\Models\Artiste;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Store\Http\Resources\ArtisteViewResource;
use Webkul\Store\Http\Resources\ArtistesListResource;

class ArtisteController extends APIController
{
    public function index(): JsonResource
    {
        $artisteslist = Artiste::all();
        return ArtistesListResource::collection($artisteslist);
    }

    public function view(): JsonResource
    {
        $artisteview = Artiste::with('products')->get();
        return ArtisteViewResource::collection($artisteview);
    }
}
