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
        $orderProducts = $order->orderProduct()->with('product')->get();

        foreach ($orderProducts as $orderProduct) {
            $product = $orderProduct->product;

            if ($product) {
                $newStock = $product->stock_count + $orderProduct->count;

                $product->update([
                    'stock_count' => $newStock
                ]);
            } else {
                Log::warning("Product not found for OrderProduct ID: {$orderProduct->id}");
            }
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
