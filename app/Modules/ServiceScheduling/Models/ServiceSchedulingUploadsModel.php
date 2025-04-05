<?php

namespace App\Modules\ServiceScheduling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSchedulingUploadsModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bms_service_scheduling_upload';

    protected $fillable = [
        'path',
        'bms_service_scheduling_id',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ServiceSchedulingModel::class, 'bms_service_scheduling_id', 'id');
    }
}