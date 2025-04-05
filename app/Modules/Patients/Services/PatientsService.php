<?php

namespace App\Modules\Patients\Services;

use App\Http\Services\BaseServices;
use App\Modules\Clients\Models\Clients;
use App\Modules\Patients\Models\PatientResponsible;
use App\Modules\Patients\Models\Patients;
use Illuminate\Support\Collection as IlluminateCollection;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingUploadsModel;
use Exception;
use Masterminds\HTML5\Entities;
use Throwable;

class PatientsService extends BaseServices
{
    private $serviceScheduling;
    private $serviceSchedulingUploads;
    private $patientResponsibleModel;
    private $client;

    public function __construct()
    {
        $this->patientResponsibleModel = new PatientResponsible();
        $this->serviceSchedulingUploads = new ServiceSchedulingUploadsModel();
        $this->serviceScheduling = new ServiceSchedulingModel();
        $this->client = new Clients();
    }
    public function newPatient(array $data)
    {
        try {
            $newPatient = Patients::create($this->createPatientArray($data));
            foreach ($this->organizeDynamicData($data, "patient_responsible", "phone_number") as $patientRes) {
                $newPatient->patientResponsible()->attach($this->validateResponsible($patientRes));
            }

            if (isset($data['entities'])) {
                $newPatient->clients()->attach($data['entities']);
            }

            return response()->json([
                'message' => 'Patient created successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Can\'t create a patient',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function listAllPatients($request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', 'all');

        $patients = Patients::with(['patientResponsible', 'clients']);

        if ($search) {
            $patients = $patients->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('patient_number', 'like', '%' . $search . '%');
            });
        }

        if ($status !== 'all') {
            $patients = $patients->where('status', $status);
        }

        if(!$request->sorter){
            $patients = $patients->orderBy('created_at', 'desc');
        }

        $patients = $patients->when($request->sorter === 'ascend', function ($query) {
            return $query->orderBy('name', 'asc');
        })
            ->when($request->sorter === 'descend', function ($query) {
                return $query->orderBy('name', 'desc');
            })
            ->paginate($request->get('perPage') ?? 10);

        return $patients;
    }

    public function editPatient(array $formData, Patients $data)
    {
        try {
            $data->update($this->createPatientArray($formData));
            $responsibleArray = $this->patientResponsibleModel->findResponsibleByPatientId($data->id)->get()->toArray();
            $validateAttach = [];
            foreach ($this->organizeDynamicData($formData, "patient_responsible", "phone_number") as $patientRes) {
                $findResponsible = collect(array_filter($responsibleArray, function ($value) use ($patientRes) {
                    return $value['id'] == $patientRes["id"];
                }));
                $index = $this->findIndexIntoArrayAndCollection($responsibleArray, $findResponsible);
                if (!$findResponsible->isEmpty()) {
                    $this->validateResponsibleEdit($patientRes);
                    array_splice($responsibleArray, $index, 1);
                } else {
                    $attach = $this->validateResponsible($patientRes);
                    $verifyIfExists = $this->patientResponsibleModel->verifyIfExistPivotValue($data->id, $attach)->first();
                    $validateAttach[] = $attach;
                    if (!$verifyIfExists) {
                        $data->patientResponsible()->attach($attach);
                    }
                }
            }

            if (isset($formData['entities'])) {
                $data->clients()->detach();
                $data->clients()->attach($formData['entities']);
            }

            $this->detachOldResponsible($responsibleArray, $validateAttach, $data);
            return response()->json([
                'message' => 'Patient edited successfully',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Can\'t edit a patient',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deletePatient(Patients $data)
    {

        try {
            if (count($schedules = $data->serviceSchedules()->get())){
               foreach($schedules as $sch) {
                    $scheduling = ServiceSchedulingModel::withTrashed()->where('id', $sch->id)->first();
                    $scheduling->patient_id = 0;
                    $scheduling->save();
                    if ($scheduling->trashed()) {
                        continue;
                    }

                    if ($uploadSchedule = ServiceSchedulingUploadsModel::where('bms_service_scheduling_id', $sch->id)->get()) {
                        foreach($uploadSchedule as $upload) {
                            ServiceSchedulingUploadsModel::where('id', $upload["id"])->delete();
                        }
                    }

                    $scheduling->delete();
                }
            }
            $data->patientResponsible()->detach();
            $data->delete();
            return response()->json([
                'message' => 'Patient deleted successfully',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Can\'t delete a patient',
                'error' => $e->getMessage()
            ]);
        }
    }
    //* PRIVATE FUNCTION
    private function validateResponsible(array $patientRes): int
    {
        $responsibleId = 0;
        $existingPatientResponsible = PatientResponsible::where('phone_number', $patientRes["phone_number"])->first();

        if ($existingPatientResponsible) {
            if ($existingPatientResponsible["patient_responsible"] !== $patientRes["patient_responsible"]) {
                $existingPatientResponsible->patient_responsible = $patientRes["patient_responsible"];
                $existingPatientResponsible->save();
            }
            $responsibleId = $existingPatientResponsible->id;
        } else {
            $responsibleId = PatientResponsible::create($patientRes)->id;
        }

        return $responsibleId;
    }

    private function validateResponsibleEdit(array $patientRes)
    {
        $patientResponsible = PatientResponsible::where('id', $patientRes["id"])->first();
        if ($patientResponsible->patient_responsible !== $patientRes["patient_responsible"]) {
            $patientResponsible->patient_responsible = $patientRes["patient_responsible"];
            $id = $patientResponsible->save();
        }

        if ($patientResponsible->patient_responsible !== $patientRes["phone_number"]) {
            $id = $this->validateResponsible($patientRes);
        }

        return $id;
    }

    private function createPatientArray(array $formData): array
    {
        return [
            "name" => $formData["name"],
            "patient_number" => $formData["patient_number"],
            "nif" => $formData["nif"],
            "birthday" => $formData["birthday"],
            "email" => isset($formData["email"]) ? $formData["email"] : "",
            "address" => $formData["address"],
            "postal_code" => $formData["postal_code"],
            "postal_code_address" => $formData["postal_code_address"],
            "transport_feature" => $formData["transport_feature"],
            "patient_observations" => $formData["patient_observations"],
            "status" => $formData["status"],
            "phone_number" => $formData["patient_phone_number"]
        ];
    }

    private function findIndexIntoArrayAndCollection(array $principal, IlluminateCollection $second)
    {
        foreach ($principal as $key => $value) {
            if (isset($second[$key])) {
                return $value["id"] === $second[$key]["id"] ? $key : null;
            }
        }
    }

    private function detachOldResponsible(array $array, array $validateAttach, Patients $data)
    {
        foreach ($array as $value) {
            if (array_search($value["id"], $validateAttach) === false) {
                $data->patientResponsible()->detach($value["id"]);
            }
        }
    }

    public function getTotal(string $type = 'all'): int
    {
        return Patients::count();
    }

    public function addResponsible($request) {
        if (isset($request['id'])) {
            $patient = Patients::find($request['id']);
            foreach ($request['responsibles'] as $responsible) {
                $patientResponsible = PatientResponsible::where('phone_number', ['phoneNumber'])->first();

                if ($patientResponsible) {
                    $patientResponsible->update(['name' => $responsible['name']]);
                }else{
                    $responsibleId = PatientResponsible::create(['patient_responsible' => $responsible['name'], 'phone_number' => $responsible['phoneNumber']])->id;
                    $patient->patientResponsible()->attach($responsibleId);
                }
            }
        }
    }

    public function patientSchedulingHistory( $patient, $numberOfSchedules = 3 ) {
        return $patient->serviceSchedules()
        ->where(function ($query) {
            $now = now();
            $query->where(function ($subQuery) use ($now) {
                $subQuery->whereDate('date', '<', $now->format('Y-m-d'))
                    ->orWhere(function ($timeQuery) use ($now) {
                        $timeQuery->whereDate('date', $now->format('Y-m-d'))
                            ->whereTime('time', '<', $now->format('H:i:s'));
                    });
            });
        })
        ->latest('date')
        ->take($numberOfSchedules)
        ->get()
        ->sortBy('time')
        ->sortBy('date');


    }

    public function patientFutureScheduling( $patient, $numberOfSchedules = 3 ){
        return $patient->serviceSchedules()
        ->where(function ($query) {
            $now = now();

            $query->where(function ($subQuery) use ($now) {
                $subQuery->whereDate('date', '>', $now->format('Y-m-d'))
                    ->orWhere(function ($timeQuery) use ($now) {
                        $timeQuery->whereDate('date', $now->format('Y-m-d'))
                            ->whereTime('time', '>', $now->format('H:i:s'));
                    });
            });
        })
        ->orderBy('date')
        ->take($numberOfSchedules)
        ->get()
        ->sortBy('time')
        ->sortBy('date');
    }

    public function getPatientsFromClients($clientId){
        $client = $this->client->findOrFail($clientId);
        $patients = $client->patients;
        return $patients;
    }


    public function getPatientDetails($patientId){
        $patient = Patients::findOrFail($patientId);
        return $patient;
    }
}
