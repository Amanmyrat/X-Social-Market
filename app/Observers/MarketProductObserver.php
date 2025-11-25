<?php

namespace App\Observers;

use App\Models\MarketProduct;

class MarketProductObserver
{
    /**
     * Handle the MarketProduct "updating" event.
     * Automatically set is_active to false when stock reaches 0
     */
    public function updating(MarketProduct $product): void
    {
        if ($product->isDirty('stock') && $product->stock <= 0) {
            $product->is_active = false;
        }
    }

    /**
     * Handle the MarketProduct "created" event.
     */
    public function created(MarketProduct $product): void
    {
        // If created with 0 stock, set inactive
        if ($product->stock <= 0) {
            $product->is_active = false;
            $product->saveQuietly();
        }
    }
}

