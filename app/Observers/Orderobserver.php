<?php

namespace App\Observers;

use App\Enum\Order\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class Orderobserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {

            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            $orderProducts = $order->orderProduct()->with('product')->get();

            if ($newStatus === OrderStatusEnum::COMPLETED) {
                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;
                    if ($product) {
                        $newStock = $product->stock_count - $orderProduct->count;
                        $product->update(['stock_count' => $newStock]);
                    }
                }
            }

            if (
                ($oldStatus instanceof OrderStatusEnum ? $oldStatus->value : $oldStatus) === OrderStatusEnum::COMPLETED->value &&
                (
                    $newStatus === OrderStatusEnum::CANCELLED ||
                    $newStatus === OrderStatusEnum::RETURNED
                )
            ) {
                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;
                    if ($product) {
                        $newStock = $product->stock_count + $orderProduct->count;
                        $product->update(['stock_count' => $newStock]);
                    }
                }
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Sadece COMPLETED siparişler için çalıştır
        if ($order->status === OrderStatusEnum::COMPLETED) {

            // Siparişe ait ürünleri ve ilişkili product kayıtlarını çek
            $orderProducts = $order->orderProduct()->with('product')->get();

            foreach ($orderProducts as $orderProduct) {
                $product = $orderProduct->product;

                if ($product) {
                    // Stok geri ekle (sipariş silindiği için iade sayılır)
                    $newStock = $product->stock_count + $orderProduct->count;

                    // Ürünü güncelle
                    $product->update([
                        'stock_count' => $newStock
                    ]);

                    // Log kaydı
                    Log::info("Order deleted: Product ID {$product->id} stock restored. New stock: {$newStock}");
                } else {
                    // Ürün bulunamadıysa logla
                    Log::warning("Order deleted: Product not found for OrderProduct ID {$orderProduct->id}");
                }
            }

        } else {
            // Eğer sipariş COMPLETED değilse logla
            Log::info("Order deleted but not COMPLETED. Order ID: {$order->id}, Status: {$order->status->value}");
        }
    }



    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
