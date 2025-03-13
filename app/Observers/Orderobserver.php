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
        Log::info("Order updated: ID {$order->id}, New Status: {$order->status}");

        if ($order->wasChanged('status') && $order->status == OrderStatusEnum::COMPLETED) {
            Log::info("Order COMPLETED detected: ID {$order->id}");

            $orderProducts = $order->orderProduct()->with('product')->get();

            foreach ($orderProducts as $orderProduct) {
                $product = $orderProduct->product;

                if ($product) {
                    $newStock = $product->stock_count - $orderProduct->count;

                    $product->update([
                        'stock_count' => $newStock
                    ]);

                    Log::info("Product ID {$product->id} stock updated to: {$newStock}");
                } else {
                    Log::warning("Product not found for OrderProduct ID: {$orderProduct->id}");
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
