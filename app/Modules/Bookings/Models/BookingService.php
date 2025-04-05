<?php

namespace App\Modules\Bookings\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Arr;

use App\Modules\Workers\Models\Worker;
use App\Modules\Services\Models\Service;
use App\Modules\Services\Models\ServiceState;
use App\Modules\Vehicles\Models\Vehicle;
use App\Modules\Routes\Models\Zone;
use App\Modules\Routes\Models\Location;

class BookingService extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'bms_booking_services';

    protected $fillable = [
        'start',
        'hour',
        'number_adults',
        'number_children',
        'number_baby_chair',
        'booster',
        'flight_number',
        'vehicle_text',
        'car_type',
        'pickup_location',
        'pickup_zone',
        'pickup_address',
        'pickup_reference_point',
        'dropoff_location',
        'dropoff_zone',
        'dropoff_address',
        'dropoff_reference_point',
        'value',
        'charge',
        'commission',
        'notes',
        'internal_notes',
        'driver_notes',
        'emphasis',
        'voucher',
        'booking_id',
        'service_type_id',
        'service_state_id',
        'staff_id',
        'supplier_id',
        'vehicle_id',
        'pickup_location_id',
        'pickup_zone_id',
        'dropoff_location_id',
        'dropoff_zone_id',
        'parent_id',
        'was_paid'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'voucher' => 'json',
        'was_paid' => 'boolean',
        'emphasis' => 'boolean'
    ];

    protected $auditInclude = [
        'start',
        'hour',
        'number_adults',
        'number_children',
        'number_baby_chair',
        'booster',
        'flight_number',
        'vehicle_text',
        'car_type',
        'pickup_location',
        'pickup_zone',
        'pickup_address',
        'pickup_reference_point',
        'dropoff_location',
        'dropoff_zone',
        'dropoff_address',
        'dropoff_reference_point',
        'value',
        'charge',
        'commission',
        'notes',
        'internal_notes',
        'driver_notes',
        'emphasis',
        'service_type_id',
        'service_state_id',
        'staff_id',
        'supplier_id',
        'vehicle_id',
        'pickup_location_id',
        'pickup_zone_id',
        'dropoff_location_id',
        'dropoff_zone_id',
        'parent_id'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function parent()
    {
        return $this->belongsTo(BookingService::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasOne(BookingService::class, 'parent_id');
    }

    public function staff()
    {
        return $this->belongsTo(Worker::class, 'staff_id')->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Worker::class, 'supplier_id')->withTrashed();
    }

    public function serviceType()
    {
        return $this->belongsTo(Service::class, 'service_type_id')->withTrashed();
    }

    public function serviceState()
    {
        return $this->belongsTo(ServiceState::class, 'service_state_id')->withTrashed();
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function pickupZone()
    {
        return $this->belongsTo(Zone::class, 'pickup_zone_id');
    }

    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    public function dropoffZone()
    {
        return $this->belongsTo(Zone::class, 'dropoff_zone_id');
    }

    public function transformAudit(array $data): array
    {
        $fields = [
            'service_type_id' => Service::class,
            'service_state_id' => ServiceState::class,
            'staff_id' => Worker::class,
            'supplier_id' => Worker::class
        ];

        foreach($fields as $field => $model) {
            if (Arr::has($data, 'new_values.'. $field)) {
                $data['old_values'][str_replace('_id', '', $field)] = $model::find($this->getOriginal($field))->name ?? '';
                $data['new_values'][str_replace('_id', '', $field)] = $model::find($this->getAttribute($field))->name ?? '';
            }
        }

        return $data;
    }

    protected static function booted()
    {
        static::forceDeleting(function(BookingService $service) {
            $service->child()->forceDelete();
        });

        static::updating(function(BookingService $service) {
            if (!empty($service->voucher)) {
                $voucher = $service->voucher;

                if ($service->wasChanged('start')) {
                    $voucher['start'] = $service->start;
                }

                if ($service->wasChanged('hour')) {
                    $voucher['hour'] = $service->hour;
                }

                if ($service->wasChanged('pickup_location')) {
                    $voucher['pickup_location'] = $service->pickup_location;
                }

                if ($service->wasChanged('dropoff_location')) {
                    $voucher['dropoff_location'] = $service->dropoff_location;
                }

                DB::table('bms_booking_services')->where('id', '=', $service->id)->update(compact('voucher'));
            }
        });
    }
}
