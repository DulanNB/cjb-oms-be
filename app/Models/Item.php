<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'length',
        'width',
        'height',
        'weight_limit',
        'volume',
        'price',
        'is_active',
        'material',
        'color',
        'stock_quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight_limit' => 'decimal:2',
        'volume' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    /**
     * Calculate volume automatically when dimensions are set.
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->length && $item->width && $item->height) {
                $item->volume = $item->length * $item->width * $item->height;
            }
        });
    }

    /**
     * Scope to get only active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get items in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Check if item is available (active and in stock).
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock_quantity > 0;
    }

    /**
     * Get formatted dimensions string.
     */
    public function getDimensionsAttribute(): string
    {
        return "{$this->length} x {$this->width} x {$this->height} cm";
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }
}
