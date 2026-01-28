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
     * Ajouter le produit téléchargeable gratuitement si le bundle a été acheté
     */
    private function addFreeDownloadableFromBundle(OrderContract $order)
    {
        try {
            // Vérifier si le bundle (ID 343) est dans la commande
            $bundleItem = $order->items->firstWhere('product_id', 343);
            
            if (! $bundleItem) {
                return;
            }

            // Récupérer le produit téléchargeable
            $downloadableProduct = \Webkul\Product\Models\Product::find(96);
            
            // CONDITION 1: Vérifier que le téléchargeable est activé
            if (! $downloadableProduct || ! $downloadableProduct->status) {
                \Log::info('Order ' . $order->id . ': Produit téléchargeable désactivé ou non trouvé');
                return;
            }

            // CONDITION 3: Vérifier que le téléchargeable contient au moins un fichier
            $linkIds = $downloadableProduct->downloadable_links->pluck('id')->toArray();
            if (empty($linkIds)) {
                \Log::info('Order ' . $order->id . ': Produit téléchargeable ne contient pas de fichiers');
                return;
            }

            // CONDITION 2: Vérifier que le produit simple (vinyle) n'est pas en précommande
            $simpleItem = $bundleItem->children->firstWhere('product_id', 93);
            if ($simpleItem) {
                $simpleProduct = \Webkul\Product\Models\Product::find(93);
                if ($simpleProduct && $simpleProduct->preorder) {
                    \Log::info('Order ' . $order->id . ': Produit simple (vinyle) est en précommande, pas d\'ajout du téléchargeable');
                    return;
                }
            }

            // Vérifier que le téléchargeable n'est pas déjà dans les enfants du bundle
            $downloadableChild = $bundleItem->children->firstWhere('product_id', 96);
            
            if ($downloadableChild) {
                \Log::info('Order ' . $order->id . ': Téléchargeable déjà dans les enfants du bundle');
                return;
            }

            \Log::info('Order ' . $order->id . ': Ajout du téléchargeable gratuit pour le bundle');

            // Récupérer le produit bundle parent
            $bundleProduct = \Webkul\Product\Models\Product::find(343);
            
            // Préparer les données du produit pour la commande
            $orderItemData = [
                'order_id' => $order->id,
                'product_id' => $downloadableProduct->id,
                'product_type' => \Webkul\Product\Models\Product::class,
                'type' => 'downloadable',
                'sku' => $downloadableProduct->sku,
                'name' => $downloadableProduct->name,
                'quantity' => 1,
                'price' => 0,
                'base_price' => 0,
                'total' => 0,
                'base_total' => 0,
                'weight' => $downloadableProduct->weight ?? 0,
                'parent_id' => $bundleItem->id,
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

            \Log::info('Order ' . $order->id . ': Téléchargeable gratuit ajouté avec succès');

        } catch (\Exception $e) {
            \Log::error('Order ' . $order->id . ': Erreur lors de l\'ajout du téléchargeable gratuit: ' . $e->getMessage());
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
