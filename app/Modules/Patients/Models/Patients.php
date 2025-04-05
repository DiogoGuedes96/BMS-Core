<?php

namespace App\Modules\Patients\Models;

use App\Modules\Clients\Models\Clients;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patients extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patients';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'patient_number',
        'nif',
        'birthday',
        'email',
        'address',
        'postal_code',
        'postal_code_address',
        'transport_feature',
        'patient_observations',
        'status',
        'phone_number',
        'credits'
    ];

    public function patientResponsible() : BelongsToMany {
        return $this->belongsToMany(
            PatientResponsible::class,
            'patient_have_responsible',
            'patient_id',
            'patient_responsible_id'
        );
    }

    public function serviceSchedules()
    {
        return $this->hasMany(ServiceSchedulingModel::class, 'patient_id', 'id');
    }

    public function clients()
    {
        return $this->belongsToMany(Clients::class, 'clients_have_patients', 'patient_id', 'client_id')
            ->withTimestamps();
    }
}
