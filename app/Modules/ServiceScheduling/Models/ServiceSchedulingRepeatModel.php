<?php

namespace App\Modules\ServiceScheduling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceSchedulingRepeatModel extends Model
{
    use HasFactory;

    protected $table = 'bms_service_scheduling_repeat';

    protected $fillable = [
        'is_repeat_schedule',
        'repeat_date',
        'repeat_time',
        'repeat_days',
        'repeat_finish_by',
        'repeat_final_date',
        'repeat_number_sessions'
    ];

    protected $casts = [
        'repeat_days' => 'array',
        'is_repeat_schedule' => 'boolean'
    ];

    // public function setRepeatDaysAttribute($value)
    // {
    //     $this->attributes['repeat_days'] = json_encode($value);
    // }

    // public function getRepeatDaysAttribute($value)
    // {
    //     return json_decode($value);
    // }
}
