<?php

namespace App\Modules\Bookings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingClient extends Model
{
    use SoftDeletes;

    protected $table = 'bms_booking_clients';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
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

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'booking_client_id');
    }
}
