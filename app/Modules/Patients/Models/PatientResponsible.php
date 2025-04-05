<?php

namespace App\Modules\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;

class PatientResponsible extends Model
{
    use HasFactory;

    protected $table = 'patient_responsible';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_responsible',
        'phone_number',
    ];

    public function patients() : BelongsToMany {
        return $this->belongsToMany(
            Patients::class,
            'patient_have_responsible',
            'patient_responsible_id',
            'patient_id'
        );
    }

    public function findResponsibleByPatientId(int $id){
        return $this->whereHas('patients', function ($query) use ($id) {
            $query->where('patient_id', $id);
        });
    }

    public function verifyIfExistPivotValue(int $patientId, int $responsibleId) {
        return $this->whereHas('patients', function ($query) use ($responsibleId, $patientId) {
            $query->where('patient_responsible_id', $responsibleId)->where('patient_id', $patientId);
        });
    }
}
