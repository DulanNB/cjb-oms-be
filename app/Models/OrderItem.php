<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'sale_amount',
        'del_fee',
        'is_invoiced',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'qty' => 'integer',
        'sale_amount' => 'decimal:2',
        'del_fee' => 'decimal:2',
        'is_invoiced' => 'boolean',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product/item associated with the order item.
     */
    public function product()
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    /**
     * Get the total amount for this order item (qty * sale_amount + del_fee).
     */
    public function getTotalAttribute(): float
    {
        return ($this->sale_amount * $this->qty) + $this->del_fee;
    }

    /**
     * Get the subtotal for this order item (qty * sale_amount).
     */
    public function getSubtotalAttribute(): float
    {
        return $this->sale_amount * $this->qty;
    }
}
