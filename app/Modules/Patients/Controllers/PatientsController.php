<?php

namespace App\Modules\Patients\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Patients\Models\Patients;
use App\Modules\Patients\Requests\AddPatientResponsibleRequest;
use App\Modules\Patients\Services\PatientsService;
use App\Modules\Patients\Requests\PatientRequest;
use App\Modules\Patients\Resources\PatientResource;
use App\Modules\Patients\Resources\PatientTotalResource;
use App\Modules\Patients\Services\PatientResponsiblesService;
use App\Modules\ServiceScheduling\Resources\ServiceSchedulingResource;
use Illuminate\Http\Request;
use Throwable;

class PatientsController extends Controller
{
    private $patientService;
    private $patientResponsiblesService;
    public function __construct()
    {
        $this->patientService = new PatientsService();
        $this->patientResponsiblesService = new PatientResponsiblesService();
    }

    public function newPatient(PatientRequest $request)
    {
        try {
            return $this->patientService->newPatient($request->all());
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant create a Patient',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function listAllPatients(Request $request)
    {
        try {
            $patient =  $this->patientService->listAllPatients($request);
            return (PatientResource::collection($patient))
                ->response()->setStatusCode(200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant list a Patient',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function editPatient(PatientRequest $request, Patients $patient)
    {
        try {
            return $this->patientService->editPatient($request->all(), $patient);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant edit a Patient',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deletePatient(Patients $patient)
    {
        try {
            return $this->patientService->deletePatient($patient);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant delete a Patient',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function total()
    {
        $total = $this->patientService->getTotal();

        return (new PatientTotalResource($total))
            ->response()->setStatusCode(200);
    }

    public function addResponsible(AddPatientResponsibleRequest $addPatientResponsibleRequest){
        try {
            return $this->patientService->addResponsible($addPatientResponsibleRequest);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error Creating Responsible',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function patientSchedulingHistory(Request $request, Patients $patient) {
        try {
            $schedulesNumber = $request->query('schedulesNumber', 3);
    
            $serviceSchedules = $this->patientService->patientSchedulingHistory($patient, $schedulesNumber);
            return (ServiceSchedulingResource::collection($serviceSchedules))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function patientFutureScheduling(Request $request, Patients $patient) {
        try {
            $schedulesNumber = $request->query('schedulesNumber', 3);
            
            $serviceSchedules = $this->patientService->patientFutureScheduling($patient, $schedulesNumber);
            return (ServiceSchedulingResource::collection($serviceSchedules))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function getPatientsFromResponsible($responsibleId){
        try {
            $clients = $this->patientResponsiblesService->getPatientsFromResponsible($responsibleId);
            return (PatientResource::collection($clients))->response()->setStatusCode(200);
        } catch (\Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }

    public function getPatientsFromClients($clientId){
        try {
            $clients = $this->patientService->getPatientsFromClients($clientId);
            return (PatientResource::collection($clients))->response()->setStatusCode(200);
        } catch (\Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }

    public function getPatientDetails($patientId){
        try {
            $patient = $this->patientService->getPatientDetails($patientId);
            return (PatientResource::make($patient))->response()->setStatusCode(200);
        } catch (\Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }
}
