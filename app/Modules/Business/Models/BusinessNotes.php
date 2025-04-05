<?php

namespace App\Modules\Business\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class BusinessNotes extends Model
{
    protected $table = 'business_notes';

    protected $fillable = [
        'business_id',
        'created_by',
        'content',
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
}
