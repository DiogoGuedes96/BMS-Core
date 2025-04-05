<?php

namespace App\Modules\Bookings\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

use App\Modules\Workers\Models\Worker;
use App\Modules\Routes\Models\Location;

class Booking extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'bms_bookings';

    protected $fillable = [
        'client_name',
        'client_email',
        'client_phone',
        'value',
        'deposits_paid',
        'pax_group',
        'emphasis',
        'status',
        'voucher',
        'operator_id',
        'booking_client_id',
        'start_date',
        'hour',
        'created_by',
        'additional_information',
        'status_reason',
        'pickup_location',
        'dropoff_location',
        'pickup_location_id',
        'dropoff_location_id',
        'was_paid',
        'reference'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'emphasis' => 'boolean',
        'voucher' => 'json',
        'was_paid' => 'boolean'
    ];

    protected $auditInclude = [
        'client_name',
        'client_email',
        'client_phone',
        'value',
        'deposits_paid',
        'pax_group',
        'emphasis',
        'status',
        'operator_id',
        'booking_client_id',
        'start_date',
        'hour',
        'pickup_location',
        'dropoff_location',
        'created_by',
        'additional_information',
        'status_reason',
        'pickup_location_id',
        'dropoff_location_id',
        'was_paid',
        'reference'
    ];

    public function operator()
    {
        return $this->belongsTo(Worker::class, 'operator_id')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(BookingClient::class, 'booking_client_id')->withTrashed();
    }

    public function services()
    {
        return $this->hasMany(BookingService::class, 'booking_id')->orderBy('start', 'asc');
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    protected static function booted()
    {
        static::deleting(function(Booking $booking) {
            $booking->services()->delete();
        });

        static::restoring(function(Booking $booking) {
            $booking->services()->restore();
        });

        static::forceDeleting(function(Booking $booking) {
            DB::table('bms_booking_services')->where('booking_id', '=', $booking->id)->update(['parent_id' => null]);

            $booking->services()->forceDelete();
        });

        static::updating(function(Booking $booking) {
            if (!empty($booking->voucher)) {
                $voucher = $booking->voucher;

                if ($booking->wasChanged('client_name')) {
                    $voucher['client_name'] = $booking->client_name;
                }

                if ($booking->wasChanged('pax_group')) {
                    $voucher['pax_group'] = $booking->pax_group;
                }

                if ($booking->wasChanged('operator')) {
                    $voucher['operator'] = $booking->operator;
                }

                DB::table('bms_bookings')->where('id', '=', $booking->id)->update(compact('voucher'));
            }
        });
    }
}
