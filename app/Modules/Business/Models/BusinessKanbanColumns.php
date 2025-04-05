<?php

namespace App\Modules\Business\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessKanbanColumns extends Model
{
    use SoftDeletes;

    protected $table = 'business_kanban_columns';

    protected $fillable = [
        'name',
        'color',
        'index',
        'business_kanban_id',
        'is_first',
        'is_last',
        'deleted_at'
    ];

    public function businessKanban()
    {
        return $this->belongsTo(BusinessKanban::class, 'business_kanban_id');
    }

    public function business()
    {
        return $this->hasMany(Business::class, 'stage');
    }
}
