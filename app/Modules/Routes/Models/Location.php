<?php

namespace App\Modules\Routes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $table = 'bms_locations';

    protected $fillable = [
        'name', 'address', 'lat', 'long', 'reference_point', 'active', 'zone_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'lat' => 'float',
        'long' => 'float'
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
