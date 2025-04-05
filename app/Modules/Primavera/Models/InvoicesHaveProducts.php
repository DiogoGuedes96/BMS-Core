<?php

namespace App\Modules\Primavera\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoicesHaveProducts extends Model
{
    use HasFactory;

    protected $table = 'invoices_have_products';
    protected $primaryKey = ['invoice_id', 'product_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'product_id',
    ];
}
