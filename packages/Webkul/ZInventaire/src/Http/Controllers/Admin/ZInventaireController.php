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
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

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
     * Update multiple inventories in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateinventaire(Request $request)
    {
        $request->validate([
            'inventories' => 'required|array',
            'inventories.*.id' => 'required|integer|exists:products,id',
            'inventories.*.qty' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->input('inventories') as $inventory) {
                    $product = $this->productRepository->findOrFail($inventory['id']);
                    $productInventory = $product->inventories()->first();

                    if ($productInventory) {
                        $productInventory->update(['qty' => $inventory['qty']]);
                    }

                    // Déclencher l'événement pour mettre à jour les indices
                    Event::dispatch('catalog.product.update.after', $product);
                }
            });

            return response()->json([
                'message' => 'Inventaire mis à jour avec succès',
                'count' => count($request->input('inventories')),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
            ], 500);
        }
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
