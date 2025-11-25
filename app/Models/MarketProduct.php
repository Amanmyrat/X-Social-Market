<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MarketProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'price_tnt',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price_tnt' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get full image URL
     */
    public function getImageAttribute(): ?string
    {
        if ($this->image_url) {
            return url(Storage::url($this->image_url));
        }
        return null;
    }

    /**
     * Get all purchases for this product
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'product_id');
    }

    /**
     * Check if product is available for purchase
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    /**
     * Scope to get only available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('stock', '>', 0);
    }
}

