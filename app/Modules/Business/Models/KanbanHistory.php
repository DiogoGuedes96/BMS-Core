<?php

namespace App\Modules\Business\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanHistory extends Model
{
    protected $table = 'kanban_history';

    protected $fillable = [
        'business_id',
        'kanban_id',
        'kanban_column_id',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function businessKanban()
    {
        return $this->belongsTo(BusinessKanban::class, 'kanban_id');
    }

    public function kanbanColumn()
    {
        return $this->belongsTo(BusinessKanbanColumns::class, 'kanban_column_id');
    }
}
