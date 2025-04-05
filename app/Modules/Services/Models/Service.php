<?php

namespace App\Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Bookings\Models\BookingService;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'bms_services';

    protected $fillable = [
        'name',
        'color',
        'active'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class, 'service_type_id');
    }
}
