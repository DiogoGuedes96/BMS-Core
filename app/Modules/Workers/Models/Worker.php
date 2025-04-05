<?php

namespace App\Modules\Workers\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Tables\Models\Table;
use App\Modules\Vehicles\Models\Vehicle;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use SoftDeletes;

    protected $table = 'bms_workers';

    protected $fillable = [
        'name',
        'phone',
        'social_denomination',
        'nif',
        'responsible_name',
        'responsible_phone',
        'address',
        'postal_code',
        'locality',
        'antecedence',
        'username',
        'email',
        'notes',
        'active',
        'type',
        'table_id',
        'user_id',
        'vehicle_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::deleting(function(Worker $worker) {
            $worker->user()->delete();
        });
    }
}
