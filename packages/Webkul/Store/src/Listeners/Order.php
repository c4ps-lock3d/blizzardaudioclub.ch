<?php

namespace Webkul\Store\Listeners;

use Webkul\Sales\Contracts\Order as OrderContract;
use Webkul\Store\Mail\Order\CanceledNotification;
use Webkul\Store\Mail\Order\CommentedNotification;
use Webkul\Store\Mail\Order\CreatedNotification;

class Order extends Base
{
    /**
     * After order is created
     *
     * @return void
     */
    public function afterCreated(OrderContract $order)
    {
        // Ajouter le produit téléchargeable gratuitement si le bundle a été acheté
        $this->addFreeDownloadableFromBundle($order);
        
        try {
            if (! core()->getConfigData('emails.general.notifications.emails.general.notifications.new_order')) {
                return;
            }

            $this->prepareMail($order, new CreatedNotification($order));
        } catch (\Exception $exception) {
            report($exception);
        }
    }

    /**
     * Ajouter les produits téléchargeables gratuitement si un produit simple est acheté via son bundle parent
     */
    private function addFreeDownloadableFromBundle(OrderContract $order)
    {
        try {
            // Parcourir tous les items de la commande
            foreach ($order->items as $item) {
                // Vérifier que c'est un bundle
                if ($item->type !== 'bundle') {
                    continue;
                }

                // Vérifier qu'il a des enfants (produits achetés)
                if (!$item->children || $item->children->isEmpty()) {
                    continue;
                }

                // Récupérer le produit bundle
                $bundleProduct = $item->product;
                if (!$bundleProduct) {
                    continue;
                }

                // Vérifier que du moins un produit simple a été acheté
                $hasSimpleChild = $item->children->contains(function ($child) {
                    return $child->type === 'simple';
                });

                if (!$hasSimpleChild) {
                    continue;
                }

                // Récupérer les options du bundle pour identifier les produits téléchargeables
                $bundleOptions = $bundleProduct->bundle_options()
                    ->with('bundle_option_products.product')
                    ->get();

                if ($bundleOptions->isEmpty()) {
                    continue;
                }

                // Parcourir les options du bundle pour trouver les produits téléchargeables
                foreach ($bundleOptions as $option) {
                    foreach ($option->bundle_option_products as $bundleOptionProduct) {
                        $optionProduct = $bundleOptionProduct->product;

                        // Vérifier que c'est un produit téléchargeable
                        if (!$optionProduct || $optionProduct->type !== 'downloadable') {
                            continue;
                        }

                        // Vérifier que le téléchargeable est activé
                        if (!$optionProduct->status) {
                            \Log::info('Order ' . $order->id . ': Produit téléchargeable ' . $optionProduct->id . ' désactivé');
                            continue;
                        }

                        // Vérifier qu'il a des fichiers
                        $linkIds = $optionProduct->downloadable_links->pluck('id')->toArray();
                        if (empty($linkIds)) {
                            \Log::info('Order ' . $order->id . ': Produit téléchargeable ' . $optionProduct->id . ' ne contient pas de fichiers');
                            continue;
                        }

                        // Vérifier que le téléchargeable n'est pas déjà dans les enfants
                        $downloadableChild = $item->children->firstWhere('product_id', $optionProduct->id);
                        if ($downloadableChild) {
                            \Log::info('Order ' . $order->id . ': Téléchargeable ' . $optionProduct->id . ' déjà dans les enfants du bundle');
                            continue;
                        }

                        \Log::info('Order ' . $order->id . ': Ajout du téléchargeable gratuit ' . $optionProduct->id . ' pour le bundle ' . $bundleProduct->id);

                        // Préparer les données du produit pour la commande
                        $orderItemData = [
                            'order_id' => $order->id,
                            'product_id' => $optionProduct->id,
                            'product_type' => \Webkul\Product\Models\Product::class,
                            'type' => 'downloadable',
                            'sku' => $optionProduct->sku,
                            'name' => $optionProduct->name,
                            'quantity' => 1,
                            'price' => 0,
                            'base_price' => 0,
                            'total' => 0,
                            'base_total' => 0,
                            'weight' => $optionProduct->weight ?? 0,
                            'parent_id' => $item->id,
                            'qty_ordered' => 1,
                            'additional' => [
                                'links' => $linkIds,
                            ],
                        ];

                        // Créer l'OrderItem
                        $orderItemRepo = app(\Webkul\Sales\Repositories\OrderItemRepository::class);
                        $orderItem = $orderItemRepo->create($orderItemData);

                        \Log::info('Order ' . $order->id . ': OrderItem créé (ID: ' . $orderItem->id . ')');

                        // Créer les liens de téléchargement
                        $downloadableLinkPurchasedRepo = app(\Webkul\Sales\Repositories\DownloadableLinkPurchasedRepository::class);
                        $downloadableLinkPurchasedRepo->saveLinks($orderItem, 'available');

                        \Log::info('Order ' . $order->id . ': Téléchargeable ' . $optionProduct->id . ' gratuit ajouté avec succès');
                    }
                }
            }

        } catch (\Exception $e) {
            \Log::error('addFreeDownloadableFromBundle ERROR: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Send cancel order mail.
     *
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return void
     */
    public function afterCanceled($order)
    {
        try {
            if (! core()->getConfigData('emails.general.notifications.emails.general.notifications.cancel_order')) {
                return;
            }

            $this->prepareMail($order, new CanceledNotification($order));
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * Send order comment mail.
     *
     * @param  \Webkul\Sales\Contracts\OrderComment  $comment
     * @return void
     */
    public function afterCommented($comment)
    {
        if (! $comment->customer_notified) {
            return;
        }

        try {
            /**
             * Email to customer.
             */
            $this->prepareMail($comment, new CommentedNotification($comment));
        } catch (\Exception $e) {
            report($e);
        }
    }
}
