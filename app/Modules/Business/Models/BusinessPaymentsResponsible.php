<?php

namespace App\Modules\Business\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class BusinessPaymentsResponsible extends Model
{
    protected $table = 'business_payments_responsible';

    protected $fillable = [
        'user_id',
        'business_id',
        'business_payment_id',
        'payment_type',
        'responsible',
        'value',
        'payment_date',
        'sequence',
        'paid_at'
    ];

    public function business()
    {
        return $this->belongsTo(BusinessPayments::class, 'business_id');
    }

    public function businessPayment()
    {
        return $this->belongsTo(BusinessPayments::class, 'business_payment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
