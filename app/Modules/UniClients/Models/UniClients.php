<?php

namespace App\Modules\UniClients\Models;

use App\Modules\Users\Models\User;
use App\Modules\Business\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniClients extends Model
{
    use SoftDeletes;

    protected $table = 'uni_clients';

    protected $fillable = [
        'email',
        'phone',
        'type',
        'name',
        'organization',
        'referencer',
        'type_business',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referencer');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true); // Adjust condition as per your actual values
    }
}
