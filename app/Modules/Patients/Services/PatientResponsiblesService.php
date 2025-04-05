<?php

namespace App\Modules\Patients\Services;

use App\Http\Services\BaseServices;
use App\Modules\Patients\Models\PatientResponsible;

class PatientResponsiblesService extends BaseServices
{
    private $patientResponsible;

    public function __construct()
    {
        $this->patientResponsible = new PatientResponsible();
    }

    public function getPatientsFromResponsible($responsibleId){
        $responsible = $this->patientResponsible->findOrFail($responsibleId);
        $patients = $responsible->patients;
        return $patients;
    }
}
