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

            $oldStatus = $order->getOriginal('status'); // string
            $newStatus = $order->status; // Enum

            // Debug için kontrol logu
            Log::info('Order status change detected', [
                'old_status' => $oldStatus,
                'new_status' => $newStatus->value,
            ]);

            $orderProducts = $order->orderProduct()->with('product')->get();

            // ✅ COMPLETED olunca stock düş
            if ($newStatus === OrderStatusEnum::COMPLETED) {

                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;

                    if ($product) {
                        $newStock = $product->stock_count - $orderProduct->count;
                        $product->update([
                            'stock_count' => $newStock
                        ]);
                        Log::info("Stock decreased for Product ID {$product->id}, New Stock: {$newStock}");
                    } else {
                        Log::warning("Product not found for OrderProduct ID: {$orderProduct->id}");
                    }
                }
            }

            // ✅ Eski status COMPLETED ve yeni status CANCELLED/RETURNED ise stok geri ekle
            if (
                $oldStatus === OrderStatusEnum::COMPLETED->value && // String karşılaştırma
                (
                    $newStatus === OrderStatusEnum::CANCELLED || // Enum karşılaştırma
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
                        Log::info("Stock increased for Product ID {$product->id}, New Stock: {$newStock}");
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
