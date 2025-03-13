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
        // Önce status gerçekten değişmiş mi, kontrol et
        if ($order->wasChanged('status')) {

            // Eski ve yeni status'leri al
            $oldStatus = $order->getOriginal('status'); // Değişmeden önceki hali (Enum objesi değil, string)
            $newStatus = $order->status; // Yeni hali (Enum objesi)

            // Sipariş ürünlerini al (product ile birlikte)
            $orderProducts = $order->orderProduct()->with('product')->get();

            // Eğer status COMPLETED olduysa -> stok düş
            if ($newStatus === OrderStatusEnum::COMPLETED) {

                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;

                    if ($product) {
                        $newStock = $product->stock_count - $orderProduct->count; // Stoktan çıkar
                        $product->update([
                            'stock_count' => $newStock
                        ]);
                        Log::info("Product ID {$product->id} stock decreased to: {$newStock}");
                    } else {
                        Log::warning("Product not found for OrderProduct ID: {$orderProduct->id}");
                    }
                }
            }

            // Eğer daha önce COMPLETED idi ve şimdi CANCELLED veya RETURNED olduysa -> stok geri ekle
            if (
                $oldStatus === OrderStatusEnum::COMPLETED->value && // Dikkat: eski status string olduğu için ->value
                (
                    $newStatus === OrderStatusEnum::CANCELLED ||
                    $newStatus === OrderStatusEnum::RETURNED
                )
            ) {
                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;

                    if ($product) {
                        $newStock = $product->stock_count + $orderProduct->count; // Stok ekle
                        $product->update([
                            'stock_count' => $newStock
                        ]);
                        Log::info("Product ID {$product->id} stock increased to: {$newStock}");
                    } else {
                        Log::warning("Product not found for OrderProduct ID: {$orderProduct->id}");
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
        //
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
