<?php

namespace App\Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Bookings\Models\BookingService;

class ServiceState extends Model
{
    use SoftDeletes;

    protected $table = 'bms_service_states';

    protected $fillable = [
        'name',
        'active',
        'is_default',
        'readonly'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
        'readonly' => 'boolean'
    ];

    public function bookingServices()
    {
        return $this->hasMany(ServiceState::class, 'service_state_id');
    }
}
