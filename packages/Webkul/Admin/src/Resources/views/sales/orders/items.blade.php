<div class="flex flex-wrap gap-1.5">
    @php
        $restCount = max($order->items->count() - 3, 0);
    @endphp

    @foreach ($order->items->take(3) as $item)
        <div class="relative">
            <div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded">
                @php
                    // Pour les bundles, essayer d'afficher l'image du produit enfant (downloadable ou simple)
                    $imageUrl = null;
                    $childImageUrl = null;
                    $hasImages = false;
                    
                    if ($item->type === 'bundle') {
                        // Charger les enfants si pas déjà chargés
                        if (!$item->relationLoaded('children')) {
                            $item->load('children.product');
                        }
                        
                        // Chercher le premier enfant (simple en priorité, puis downloadable)
                        if ($item->children && $item->children->count()) {
                            // D'abord chercher un produit simple avec des images
                            foreach ($item->children as $child) {
                                if ($child->type === 'simple' && $child->product && $child->product->images->count() > 0) {
                                    $childImageUrl = $child->product->base_image_url;
                                    $hasImages = true;
                                    break;
                                }
                            }
                            
                            // Si pas de simple trouvé, chercher un produit downloadable avec des images
                            if (!$childImageUrl) {
                                foreach ($item->children as $child) {
                                    if ($child->type === 'downloadable' && $child->product && $child->product->images->count() > 0) {
                                        $childImageUrl = $child->product->base_image_url;
                                        $hasImages = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    
                    // Utiliser l'image de l'enfant si disponible, sinon utiliser l'image du parent
                    if ($childImageUrl) {
                        $imageUrl = $childImageUrl;
                    } else {
                        $imageUrl = $item->product?->base_image_url;
                        $hasImages = $item->product?->images->count() > 0;
                    }
                @endphp

                @if ($hasImages && $imageUrl)
                    <img 
                        class="h-full w-full rounded" 
                        src="{{ $imageUrl }}"
                    >

                    <span class="absolute bottom-px rounded-full bg-darkPink px-1.5 text-xs font-bold leading-normal text-white ltr:left-px rtl:right-px">
                        {{ $item->qty_ordered }}
                    </span>
                @else
                    <div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                        <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">
                        
                        <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400"> 
                            @lang('admin::app.sales.invoices.view.product-image') 
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @if ($restCount >= 1)
        <a href="{{ route('admin.sales.orders.view', $order->id) }}">
            <div class="flex h-[65px] w-[65px] items-center rounded bg-gray-50 dark:bg-gray-800">
                <p class="px-1.5 py-1.5 text-center text-xs font-bold text-gray-600 dark:text-gray-300">
                    @lang('admin::app.sales.orders.index.datagrid.product-count', ['count' => $restCount])
                </p>
            </div>
        </a>
    @endif
</div>