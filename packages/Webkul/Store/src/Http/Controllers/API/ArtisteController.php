<?php
namespace Webkul\Store\Http\Controllers\API;

use App\Models\Artiste;

class ArtisteController extends APIController
{
    public function index()
    {
        return Artiste::all();
    }
}
