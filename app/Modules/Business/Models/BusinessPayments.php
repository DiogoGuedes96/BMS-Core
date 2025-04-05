<?php

namespace App\Modules\Business\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPayments extends Model
{
    protected $table = 'business_payments';

    protected $fillable = [
        'id',
        'business_id',
        'value',
        'payment_date',
        'paid_at'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
