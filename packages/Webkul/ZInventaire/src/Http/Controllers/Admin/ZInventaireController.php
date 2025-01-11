<?php

namespace Webkul\ZInventaire\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ZInventaireController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductInventoryRepository $productInventoryRepository,
        protected ProductRepository $productRepository,
    ) {}

            /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateinventaire(Request $request, int $id)
    {
        $request->validate([
            'qty' => 'required|numeric|min:0',
        ]);
    
        $product = $this->productRepository->findOrFail($id);
        $inventory = $product->inventories()->first();
    
        if ($inventory) {
            $inventory->update(['qty' => $request->qty]);
        }
    
        return response()->json([
            'message' => 'Quantité mise à jour avec succès',
            'data' => [
                'qty' => $request->qty
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('zinventaire::admin.index');
    }
}
