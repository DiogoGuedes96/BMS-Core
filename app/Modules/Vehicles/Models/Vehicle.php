<?php

namespace App\Modules\Vehicles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Workers\Models\Worker;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $table = 'bms_vehicles';

    protected $fillable = [
        'brand',
        'model',
        'group',
        'license',
        'km',
        'max_capacity',
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


    public function staff()
    {
        return $this->hasMany(Worker::class, 'vehicle_id')->where('type', '=', 'staff');
    }
}
