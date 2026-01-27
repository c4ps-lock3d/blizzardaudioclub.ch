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

            // Vérifier que le téléchargeable n'est pas déjà dans les enfants du bundle
            $downloadableChild = $bundleItem->children->firstWhere('product_id', 96);
            
            if ($downloadableChild) {
                \Log::info('Order ' . $order->id . ': Téléchargeable déjà dans les enfants du bundle');
                return;
            }

            \Log::info('Order ' . $order->id . ': Ajout du téléchargeable gratuit pour le bundle');

            // Récupérer le produit téléchargeable
            $downloadableProduct = \Webkul\Product\Models\Product::find(96);
            
            if (! $downloadableProduct) {
                \Log::error('Order ' . $order->id . ': Produit téléchargeable (ID 96) non trouvé');
                return;
            }

            // Récupérer le produit bundle parent
            $bundleProduct = \Webkul\Product\Models\Product::find(343);
            
            // Récupérer les IDs des liens téléchargeables
            $linkIds = $downloadableProduct->downloadable_links->pluck('id')->toArray();
            
            // Préparer les données du produit pour la commande
            $orderItemData = [
                'order_id' => $order->id,
                'product_id' => $downloadableProduct->id,
                'product_type' => \Webkul\Product\Models\Product::class,
                'type' => 'downloadable',
                'sku' => $downloadableProduct->sku,
                'name' => $downloadableProduct->name,
                'description' => $downloadableProduct->description,
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

            // Mettre à jour les noms des fichiers téléchargés avec le slug du bundle parent
            $bundleSlug = $bundleProduct->url_key;
            $downloadedLinks = \Webkul\Sales\Models\DownloadableLinkPurchased::where('order_item_id', $orderItem->id)->get();
            
            foreach ($downloadedLinks as $downloadedLink) {
                // Récupérer le chemin complet du fichier
                $oldPath = storage_path('app/private/' . $downloadedLink->file);
                
                if (file_exists($oldPath)) {
                    // Créer le nouveau nom du fichier
                    $fileExtension = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $newFileName = $bundleSlug . '.' . $fileExtension;
                    
                    // Créer le nouveau chemin (même répertoire)
                    $directory = dirname($oldPath);
                    $newPath = $directory . '/' . $newFileName;
                    
                    // Renommer le fichier
                    if (rename($oldPath, $newPath)) {
                        // Mettre à jour le chemin dans la base de données
                        $relativePath = 'product_downloadable_links/' . $downloadableProduct->id . '/' . $newFileName;
                        
                        $downloadedLink->update([
                            'file' => $relativePath,
                            'file_name' => $newFileName,
                        ]);
                        
                        \Log::info('Order ' . $order->id . ': Fichier téléchargé renommé en: ' . $newFileName);
                    }
                }
            }

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
