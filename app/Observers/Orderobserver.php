<?php

namespace App\Observers;

use App\Enum\Order\OrderStatusEnum;
use App\Models\Order;

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
        if ($order->wasChanged('status') && $order->status == OrderStatusEnum::COMPLETED->value) {

            $orderProducts = $order->orderProduct;

            foreach ($orderProducts as $orderProduct) {
                $product = $orderProduct->product;

                if ($product) {
                    $newStock = $product->stock_count - $orderProduct->count;
                    $product->update([
                        'stock_count' => $newStock
                    ]);
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
