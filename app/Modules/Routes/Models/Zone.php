<?php

namespace App\Modules\Routes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bms_zones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'active'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function routesFromZone()
    {
        return $this->hasMany(Route::class, 'from_zone_id')->whereNull('deleted_at');
    }

    public function routesToZone()
    {
        return $this->hasMany(Route::class, 'to_zone_id')->whereNull('deleted_at');
    }

    public function locations()
    {
        return $this->hasMany(Location::class)->whereNull('deleted_at');
    }

    protected static function booted()
    {
        static::updated(function(Zone $zone) {
            if ($zone->wasChanged('name')) {
                $zone->name;

                DB::table('bms_booking_services')
                    ->where('pickup_zone_id', '=', $zone->id)
                    ->update(['pickup_zone' => $zone->name]);

                DB::table('bms_booking_services')
                    ->where('dropoff_zone_id', '=', $zone->id)
                    ->update(['dropoff_zone' => $zone->name]);
            }
        });
    }
}
