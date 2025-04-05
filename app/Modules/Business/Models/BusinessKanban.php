<?php

namespace App\Modules\Business\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessKanban extends Model
{
    protected $table = 'business_kanban';

    protected $fillable = [
        'type',
    ];

    public function businessKanbanColumns()
    {
        return $this->hasMany(BusinessKanbanColumns::class, 'business_kanban_id');
    }
}
