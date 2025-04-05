<?php

namespace App\Modules\ServiceScheduling\Models;

use App\Modules\Clients\Models\Clients;
use App\Modules\Patients\Models\Patients;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceSchedulingModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bms_service_scheduling';

    protected $fillable = [
        'reason',
        'additional_note',
        'patients_status',
        'patient_id',
        'patient_name',
        'patient_number',
        'patient_nif',
        'patient_contact',
        'transport_feature',
        'service_type',
        'date',
        'time',
        'origin',
        'destination',
        'vehicle',
        'license_plate',
        'responsible_tats_1',
        'responsible_tats_2',
        'companion',
        'companion_name',
        'companion_contact',
        'transport_justification',
        'payment_method',
        'total_value',
        'client_id',
        'user_id',
        'parent_id',
        'is_back_service',
        'associated_schedule',
        'repeat_date',
        'repeat_time',
        'repeat_days',
        'repeat_finish_by',
        'repeat_number_sessions',
        'repeat_final_date',
        'repeat_id',
        'used_credits'
    ];

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    public function repeat()
    {
        return $this->belongsTo(ServiceSchedulingRepeatModel::class, 'repeat_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(ServiceSchedulingUploadsModel::class, 'bms_service_scheduling_id', 'id');
    }

    public function canceled(): HasOne
    {
        return $this->hasOne(ServiceSchedulingCanceledModel::class, 'bms_service_scheduling_id', 'id');
    }
}
