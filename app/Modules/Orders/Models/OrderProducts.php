<?php

namespace App\Modules\Orders\Models;

use App\Modules\Products\Models\BmsProductsBatch;
use App\Modules\Products\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProducts extends Model
{
    use HasFactory;

    protected $table = 'order_products';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'unit_price',
        'total_liquid_price',
        'order_id',
        'bms_product',
        'correction_price_percent',
        'discount_percent',
        'discount_value',
        'sale_unit',
        'sale_price',
        'notes',
        'conversion',
        'volume',
        'unavailability',
        'bms_products_batch'
    ];

    /**
     * Returns the order associated with the current orderProduct
     *
     * @return The order that the order item belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'order_id');
    }

    public function bmsProduct()
    {
        return $this->hasOne(Products::class, 'id', 'bms_product');
    }

    public function productBatch()
    {
        return $this->hasOne(BmsProductsBatch::class, 'id', 'bms_product_batch');
    }
}
