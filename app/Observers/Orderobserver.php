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

            $oldStatus = $order->getOriginal('status'); // String geliyor
            $newStatus = $order->status; // Enum objesi

            $orderProducts = $order->orderProduct()->with('product')->get();

            // Eğer yeni durum COMPLETED ise stok düş
            if ($newStatus === OrderStatusEnum::COMPLETED) {

                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;

                    if ($product) {
                        $newStock = $product->stock_count - $orderProduct->count;
                        $product->update([
                            'stock_count' => $newStock
                        ]);
                        Log::info("Product ID {$product->id} stock decreased to: {$newStock}");
                    } else {
                        Log::warning("Product not found for OrderProduct ID: {$orderProduct->id}");
                    }
                }
            }

            // Eğer eski durum COMPLETED ve yeni durum CANCELLED veya RETURNED ise stok artır
            if (
                $oldStatus === OrderStatusEnum::COMPLETED->value && // String karşılaştırması ✅
                (
                    $newStatus === OrderStatusEnum::CANCELLED || // Enum karşılaştırması ✅
                    $newStatus === OrderStatusEnum::RETURNED
                )
            ) {
                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;

                    if ($product) {
                        $newStock = $product->stock_count + $orderProduct->count;
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
