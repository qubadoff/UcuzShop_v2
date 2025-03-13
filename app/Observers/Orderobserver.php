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

            $oldStatus = $order->getOriginal('status'); // String geliyor olabilir
            $newStatus = $order->status; // Enum objesi

            Log::info('Order status change detected', [
                'old_status' => $oldStatus instanceof OrderStatusEnum ? $oldStatus->value : $oldStatus,
                'new_status' => $newStatus->value,
            ]);

            $orderProducts = $order->orderProduct()->with('product')->get();

            // Eğer COMPLETED olduysa, stok azalt
            if ($newStatus === OrderStatusEnum::COMPLETED) {
                foreach ($orderProducts as $orderProduct) {
                    $product = $orderProduct->product;
                    if ($product) {
                        $newStock = $product->stock_count - $orderProduct->count;
                        $product->update(['stock_count' => $newStock]);
                        Log::info("Stock decreased for Product ID {$product->id}, New Stock: {$newStock}");
                    }
                }
            }

            // Eğer COMPLETED'den CANCELLED/RETURNED olduysa, stok geri ekle
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
                        Log::info("Stock increased for Product ID {$product->id}, New Stock: {$newStock}");
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
