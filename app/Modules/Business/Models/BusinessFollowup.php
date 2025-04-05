<?php

namespace App\Modules\Business\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class BusinessFollowup extends Model
{
    protected $table = 'business_followup';

    protected $fillable = [
        'business_id',
        'created_by',
        'responsible_id',
        'date',
        'time',
        'title',
        'is_important',
        'completed',
        'acId'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
