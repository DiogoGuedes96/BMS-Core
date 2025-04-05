<?php

namespace App\Modules\UniClients\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReferrerChangeRequest extends Model
{
    protected $fillable = [
        'referrer_id',
        'client_id',
        'reason',
        'status',
        'requested_by',
        'approved_by',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(UniClients::class);
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
