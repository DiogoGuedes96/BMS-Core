<?php

namespace App\Modules\Routes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Tables\Models\Table;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bms_routes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'to_zone_id',
        'from_zone_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function fromZone()
    {
        return $this->belongsTo(Zone::class, 'from_zone_id');
    }

    public function toZone()
    {
        return $this->belongsTo(Zone::class, 'to_zone_id');
    }

    public function tables()
    {
        return $this->belongsToMany(Table::class, 'bms_table_routes', 'route_id', 'table_id')->whereNull('deleted_at');
    }
}
