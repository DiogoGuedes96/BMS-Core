<?php

namespace App\Modules\ServiceScheduling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceSchedulingCanceledModel extends Model
{
    use HasFactory;

    protected $table = 'bms_service_scheduling_canceled';

    protected $fillable = [
        'bms_service_scheduling_id',
        'canceled_reason',
        'canceled_name',
        'canceled_client_patient',
        'canceled_through'
    ];

    public function scheduling(): HasOne
    {
        return $this->hasOne(ServiceSchedulingModel::class);
    }
}
