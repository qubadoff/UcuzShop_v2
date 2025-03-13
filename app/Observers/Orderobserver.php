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

            Log::info('Order status change detected', [
                'old_status' => $oldStatus instanceof OrderStatusEnum ? $oldStatus->value : $oldStatus,
                'new_status' => $newStatus->value,
            ]);

            $orderProducts = $order->orderProduct()->with('product')->get();

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
        // İlgili siparişteki ürünleri çek (product ile birlikte)
        $orderProducts = $order->orderProduct()->with('product')->get();

        foreach ($orderProducts as $orderProduct) {
            $product = $orderProduct->product;

            if ($product) {
                // Stok geri ekle (iade gibi)
                $newStock = $product->stock_count + $orderProduct->count;

                $product->update([
                    'stock_count' => $newStock
                ]);

                // Log yaz (izleme için)
                Log::info("Stock restored for Product ID {$product->id}, New Stock: {$newStock}");
            } else {
                // Ürün bulunamazsa logla
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
