<?php

namespace App\Modules\Tables\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Routes\Models\Route;
use App\Modules\Workers\Models\Worker;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;

    protected $table = 'bms_tables';

    protected $fillable = [
        'name', 'type', 'active'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function getWorkerLabel()
    {
        $workers = [
            'operators' => 'operadores',
            'suppliers' => 'fornecedores',
            'staff' => 'staff'
        ];

        return $workers[$this->type];
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'bms_table_routes', 'table_id', 'route_id')
            ->withPivot('pax14', 'pax58')
            ->withTimestamps();
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
}
