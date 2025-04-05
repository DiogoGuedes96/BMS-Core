<?php

namespace App\Modules\ServiceScheduling\Services;

use App\Helpers\FileHelper;
use App\Http\Services\BaseServices;
use App\Modules\Patients\Models\Patients;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingCanceledModel;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingRepeatModel;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingUploadsModel;
use Carbon\Carbon;
use DateTime;
use Exception;

class ServiceSchedulingService extends BaseServices
{
    private $serviceScheduling;
    private $serviceSchedulingUploads;
    private $serviceScheduleCanceled;
    private $serviceSchedulingRepeatModel;
    private $patient;

    private $weekDays = [
        '1' => 'monday',
        '2' => 'tuesday',
        '3' => 'wednesday',
        '4' => 'thursday',
        '5' => 'friday',
        '6' => 'saturday',
        '7' => 'sunday'
    ];

    public function __construct()
    {
        $this->serviceScheduling = new ServiceSchedulingModel();
        $this->serviceSchedulingRepeatModel = new ServiceSchedulingRepeatModel();
        $this->serviceSchedulingUploads = new ServiceSchedulingUploadsModel();
        $this->serviceScheduleCanceled = new ServiceSchedulingCanceledModel();
        $this->patient = new Patients();
    }
    public function list($request)
    {
        $search = $request->get('search', '');
        $searchStartDate = $request->get('searchStartDate');
        $searchEndDate = $request->get('searchEndDate');

        $serviceModel = ServiceSchedulingModel::with([
            'patient',
            'client',
            'user',
            'parent',
            'uploads',
            'repeat',
        ]);

        if ($request->filled('sorter')) {
            $sorterKey = $request->filled('sorterKey') ? $request->input('sorterKey') : 'patient_name';
            $direction = $request->input('sorter') === 'ascend' ? 'asc' : 'desc';

            if ($sorterKey === 'patient_name') {
                $serviceModel->join('patients', 'bms_service_scheduling.patient_id', '=', 'patients.id')
                    ->orderBy('patients.name', $direction)
                    ->select('bms_service_scheduling.*');
            } else {
                $serviceModel->orderBy($sorterKey, $direction);
            }
        } else {

            $serviceModel->orderByRaw("date ASC, time ASC");
        }

        if ($search) {
            $serviceModel = $serviceModel->where(function ($query) use ($search) {
                $query->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('patient_number', 'like', '%' . $search . '%');
                });
            });
        }

        if ($searchStartDate || $searchEndDate) {
            $serviceModel = $serviceModel->where(function ($query) use ($searchStartDate, $searchEndDate) {
                if ($searchStartDate && $searchEndDate) {
                    $query->whereBetween('date', [$searchStartDate, $searchEndDate]);
                } elseif ($searchStartDate) {
                    $query->where('date', '>=', $searchStartDate);
                } elseif ($searchEndDate) {
                    $query->where('date', '<=', $searchEndDate);
                }
            });
        }

        $serviceModel = $serviceModel->paginate($request->get('perPage') ?? 10);

        return $serviceModel;
    }

    public function getTotal(string $type = 'all'): int
    {
        return ServiceSchedulingModel::count();
    }

    private function calculateFutureDays($data, $array, $total = null)
    {
        $schData = [];
        $startDate = $data["repeat_date"];
        $repeatDays = $data["repeat_days"];
        $time = $data["repeat_time"];
        if ($data["repeat_finish_by"] === "date") {
            $endDate = $data["repeat_final_date"];
            $days = $this->getDatesbyWeekDays($startDate, $endDate, $repeatDays, $this->weekDays);
        }

        if ($data["repeat_finish_by"] === "sessions") {
            $days = $this->getDatesBySessions($startDate, $repeatDays, $total ?? $data["repeat_number_sessions"], $this->weekDays);
        }

        foreach ($days as $day) {
            $newData = $array;
            $newData['date'] = $day;
            $newData['time'] = $time;

            array_push($schData, $newData);

            if ($newData["is_back_service"] === "yes") {
                $newData["origin"] = $data["back_service_origin_address"];
                $newData["destination"] = $data["back_service_destiny_address"];
                array_push($schData, $newData);
            }
        }

        return $schData;
    }

    public function newSchedule(array $data)
    {
        $days = [];
        $patient = Patients::where('id', $data['patient_id'])->first();
        $data["patient_id"] = $patient->id;

        if (!empty($data['uploads'])) {
            $files = [];
            foreach ($data['uploads'] as $index => $file) {
                array_push($files, [FileHelper::fromBase64($file["base64"]), $file["name"]]);
            }
            $data['file'] = $files;
        }

        $repeatModel = null;

        if (isset($data['is_repeat_schedule'])) {
            $repeatModel = $this->createScheduleRepeat($data);
        }

        $array = $this->createSchedulingArrayDefault($data, $repeatModel);
        $schData = [];

        if (isset($data["is_repeat_schedule"])) {
            $schData = $this->calculateFutureDays($data, $array);
        }

        if (!isset($data["is_repeat_schedule"])) {

            array_push($schData, $array);

            if ($data["is_back_service"] === "yes") {
                $array["origin"] = $data["back_service_origin_address"];
                $array["destination"] = $data["back_service_destiny_address"];
                array_push($schData, $array);
            }
        }

        $filePath = null;
        $parentId = null;
        $credits = 0;
        if (isset($data["use_credits"])){
            $credits = $patient->credits;
        }
        foreach ($schData as $key => $item) {
            $item['parent_id'] = $parentId;
            $item["used_credits"] = $credits ? true : false;
            if (isset($scheduling) && $scheduling->is_back_service === "yes") {
                $item["associated_schedule"] = $scheduling->id;
                $scheduling = $this->serviceScheduling->create($item);
                $scheduling->is_back_service = "no";
            } else {
                $scheduling = $this->serviceScheduling->create($item);
            }

            if ($credits)
                $credits -= 1;

            if ($key === 0) {
                $scheduling->update(["parent_id" => $scheduling->id]);
                $parentId = $scheduling->id;
            }
            if ($filePath) {
                $this->associatedFilesInScheduling($filePath, $scheduling->id);
            } else {
                $filePath = $this->storage($scheduling->id, $item);
                $this->associatedFilesInScheduling($filePath, $scheduling->id);
            }
        }
        if (isset($data["use_credits"]))
        {
            $patient->update(["credits" => $credits]);
        }
        return $scheduling;
    }

    public function editSchedule(ServiceSchedulingModel $schedule, array $data)
    {
        $data['client_id'] = $data['client'];

        if (!empty($data['uploads'])) {
            $newFiles = [];
            $filesToKeep = [];
            foreach ($data['uploads'] as $index => $file) {
                if (!empty($file['url'])) {
                    array_push($filesToKeep, $file);
                } else {
                    array_push($newFiles, [FileHelper::fromBase64($file["base64"]), $file["name"]]);
                }
            }
            $data['file'] = $newFiles;
        }
        if (!empty($filesToKeep)) { //Schedule tem uploads e é pra manter ficheiros antigos

            $uploadIdsToDelete = [];

            foreach ($schedule->uploads as $upload) {
                $uidExists = collect($filesToKeep)->contains('uid', $upload->id);

                if (!$uidExists) {
                    $uploadIdsToDelete[] = $upload->id;
                }
            }

            if (!empty($uploadIdsToDelete)) {
                $this->serviceSchedulingUploads->whereIn('id', $uploadIdsToDelete)->delete();
            }
        } else {
            if ($schedule->uploads->isNotEmpty()) {
                $uploadIdsToDelete = $schedule->uploads->pluck('id')->toArray();

                $this->serviceSchedulingUploads->whereIn('id', $uploadIdsToDelete)->delete();
            }
        }
        if (!empty($data['file'])) {
            $filePath = $this->storage($schedule->id, $data);
            $this->associatedFilesInScheduling($filePath, $schedule->id);
        }
        $repeat = null;
        $recalculateDays = false;
        if (!empty($schedule->repeat_id) && !empty($data['is_repeat_schedule'])) {
            $repeat = $this->serviceSchedulingRepeatModel->find($schedule->repeat_id);

            if ($this->validateIfThereChanges($repeat, $data)) {
                $repeat = $this->createScheduleRepeat($data);
                $recalculateDays = true;
            }
        } else if (!empty($schedule->repeat_id)) {
            $repeat = $this->serviceSchedulingRepeatModel->find($schedule->repeat_id);
        }
        $editData = $this->createSchedulingArrayDefault($data, $repeat);
        
        if ($editData["is_back_service"] === "yes") {
            //dd($schedule->is_back_service);
            if ($schedule->is_back_service === "yes") {
                return ['error' => 'Este agendamento já tem um retorno'];
            }

            if ($schedule->associated_schedule) {
                return ['error' => 'Não pode criar um retorno sobe um retorno'];
            }

            if ($schedule->is_back_service === "no") {
                $item = $editData;
                $item["origin"] = $data["back_service_origin_address"];
                $item["destination"] = $data["back_service_destiny_address"];
                $item["associated_schedule"] = $schedule->id;
                $this->serviceScheduling->create($item);
            }
        }
        
        if ($recalculateDays) {
            
            $nextEvents = $this->getsTotalOfRemainingEvents($schedule);
           
            $schData = $this->calculateFutureDays($data, $editData, count($nextEvents));
            
            foreach ($schData as $key => $data) {
                if (isset($nextEvents[$key])) {
                    $nextEvents[$key]->update($data);
                }
            }
        } else {
            $schedule->update($editData);
        }
    }

    private function getsTotalOfRemainingEvents($schedule) {
        return $this->serviceScheduling
            ->where("id", ">=", $schedule->id)
            ->where("parent_id", $schedule->parent_id)
            ->orderBy("date", "asc")
            ->get();
    }

    public function deleteSchedule($schedule)
    {
        $patientScheduling = 0;
        $credits = 0;
        foreach ($schedule["checkbox"] as $sch) {
            $scheduling = ServiceSchedulingModel::withTrashed()->where('id', $sch)->first();
            if (!$patientScheduling) {
                $patientScheduling = $scheduling->patient;
            }

            if ($scheduling->trashed()) {
                continue;
            }

            if ($uploadSchedule = ServiceSchedulingUploadsModel::where('bms_service_scheduling_id', $sch)->get()) {
                foreach ($uploadSchedule as $upload) {
                    ServiceSchedulingUploadsModel::where('id', $upload["id"])->delete();
                }
            }

            if (!empty($schedule['upload'])) {
                $files = [];
                foreach ($schedule['upload'] as $index => $file) {
                    if ($file["id"] === $sch)
                        array_push($files, [FileHelper::fromBase64($file["base64"]), $file["name"]]);
                }
                $schedule['file'] = $files;
            }

            $filePath = $this->storage($sch . '/canceled', $schedule);
            $this->associatedFilesInSchedulingCanceled($filePath, $sch);

            $this->serviceScheduleCanceled->create([
                'bms_service_scheduling_id' => $sch,
                'canceled_reason' => $schedule["reason"],
                'canceled_name' => $schedule["name"],
                'canceled_client_patient' => $schedule["client_patient"],
                'canceled_through' => $schedule["canceled_through"] ?? ''
            ]);
            
            if ($scheduling->repeat_id) {
                $credits++;
            }

            $scheduling->delete();
        }


        if ($credits) {
            $patientScheduling->increment('credits', $credits);
        }
    }

    public function validateIfThereChanges($oldRepeat, $data)
    {
        $currentRepeat = array(
            'repeat_date' => $data['repeat_date'] ? $data['repeat_date'] . " 00:00:00" : null,
            'repeat_time' => $data['repeat_time'] ? $data['repeat_time'] . ":00" : null,
            'repeat_days' => $data['repeat_days'] ?? null,
            'repeat_finish_by' => $data['repeat_finish_by'] ?? null,
            'repeat_final_date' => $data['repeat_final_date'] ?? null,
            'repeat_number_sessions' => $data['repeat_number_sessions'] ?? null,
        );

        $oldRepeatArray = $oldRepeat->toArray();

        unset($oldRepeatArray['id']);
        unset($oldRepeatArray['created_at']);
        unset($oldRepeatArray['updated_at']);

        $diff = array_udiff_assoc($currentRepeat, $oldRepeatArray, function ($a, $b) {
            if (is_array($a) && is_array($b)) {
                return $a <=> $b;
            }
            return is_null($a) && is_null($b) ? 0 : strcmp($a, $b);
        });

        if (!empty($diff)) {
            return true;
        }

        return false;
    }

    private function createScheduleRepeat($data)
    {
        $repeat = array(
            'repeat_date' => $data['repeat_date'] ?? null,
            'repeat_time' => $data['repeat_time'] ?? null,
            'repeat_days' => $data['repeat_days'] ?? null,
            'repeat_finish_by' => $data['repeat_finish_by'] ?? null,
            'repeat_final_date' => $data['repeat_final_date'] ?? null,
            'repeat_number_sessions' => $data['repeat_number_sessions'] ?? null,
        );

        return $this->serviceSchedulingRepeatModel->create($repeat);
    }

    private function createSchedulingArrayDefault($data, $repeat = null)
    {
        $array = [
            "user_id" => auth()->user()->id,
            "client_id" => $data["client"],
            "patient_id" => $data["patient_id"],
            "reason" => $data["reason"],
            "additional_note" => $data["additional_note"] ?? '',
            "patients_status" => $data["patients_status"],
            "transport_feature" => $data["transport_feature"],
            "service_type" => $data["service_type"] ?? '',
            "date" => $data["schedule_date"] ?? $data["repeat_date"],
            "time" => $data["schedule_time"] ?? $data["repeat_time"],
            "origin" => $data["origin_address"],
            "destination" => $data["destiny_address"],
            "vehicle" => $data["vehicle"] ?? '',
            "license_plate" => $data["vehicle_registration"] ?? '',
            "responsible_tats_1" => $data["tat_1"] ?? '',
            "responsible_tats_2" => $data["tat_2"] ?? '',
            "transport_justification" => $data["transport_justification"] ?? '',
            "payment_method" => $data["payment_mode"] ?? '',
            "total_value" => $data["total_value"] ?? 0,
            "is_back_service" => $data["is_back_service"],
            "file" => $data["file"] ?? '',
            "companion" => $data["companion"] ?? 'no',
            "repeat_id" => !empty($repeat) ? $repeat->id : null
        ];

        if ($array["companion"]) {
            $array["companion_name"] = $data["companion_name"];
            $array["companion_contact"] = $data["companion_number"];
        }

        return $array;
    }

    public function getDatesbyWeekDays($startDate, $endDate, $days, $weekDays)
    {
        $dates = [];
        $currentDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        while ($currentDate <= $endDate) {
            if (in_array($weekDays[$currentDate->format('N')], $days)) {
                $dates[] = $currentDate->format('Y-m-d');
            }
            $currentDate->modify('+1 day');
        }
        return $dates;
    }

    public function getDatesBySessions($startDate, $days, $sessions, $weekDays)
    {
        $dates = [];
        $currentDate = new DateTime($startDate);
        $i = 1;
        while ($i <= $sessions) {
            if (in_array($weekDays[$currentDate->format('N')], $days)) {
                $dates[] = $currentDate->format('Y-m-d');
                $i++;
            }
            $currentDate->modify('+1 day');
        }
        return $dates;
    }

    public function associatedFilesInScheduling($filePath, $id)
    {
        if ($filePath)
            foreach ($filePath as $file) {
                $info = [
                    'path' => $file,
                    'bms_service_scheduling_id' => $id
                ];
                $this->serviceSchedulingUploads->create($info);
            }
    }

    public function restoreCanceledSchedule($schedule)
    {
        if ($schedule->exists) {
            $date = Carbon::parse($schedule->date)->format('Y-m-d');
            $combinedDateTime = Carbon::parse("{$date} {$schedule->time}");
            if (!$combinedDateTime->isPast()) {
                if ($schedule->trashed()) {
                    $schedule->restore();
                    $this->serviceScheduleCanceled->where('bms_service_scheduling_id', $schedule->id)->delete();
                    if ($schedule->patient->credits && $schedule->repeat_id) {
                        $schedule->patient->decrement('credits');
                    }
                }
            } else {
                throw new Exception('It is not possible to restore a schedule in the past', 422);
            }
        }
    }

    public function associatedFilesInSchedulingCanceled($filePath, $id)
    {
        if ($filePath)
            foreach ($filePath as $file) {
                $info = [
                    'path' => $file,
                    'bms_service_scheduling_id' => $id,
                    'canceled' => 1
                ];
                $this->serviceSchedulingUploads->create($info);
            }
    }

    public function getRepeatSchedulePosition($schedule)
    {
        $serviceModel = ServiceSchedulingModel::with([
            'patient',
            'client',
            'user',
            'parent'=> function ($query) {
                $query->withTrashed();
            },
            'uploads',
            'repeat',
        ])->orderBy('date')
        ->get()
        ->sortBy('time')
        ->sortBy('date')
        ->toArray();
        $parentId = isset($schedule['parent_id']) ? $schedule['parent_id'] : $schedule['id'];
        $filterScheduling = array_filter($serviceModel, function ($schedules) use ($parentId) {
            return $this->validateIfRepeatingValueExists($schedules, $parentId);
        });

        $position = array_search($schedule['id'], array_column($filterScheduling, 'id'));
        return ['total' => count($filterScheduling), 'position' => ($position+1)];
    }

    private function validateIfRepeatingValueExists ($schedules, $schedule) {
        if (isset($schedules['parent']) && isset($schedules['parent']['id'])) {
            return $schedules['parent']['id'] === $schedule;
        }

        if (isset($schedules['id'])) {
            return $schedules['id'] === $schedule;
        }
    }

    public function getSchedulesFromPatient($patientId, $withoutReturns) {
        $patient = $this->patient->findOrFail($patientId);
    
        $futureSchedules = $patient->serviceSchedules()
            ->where(function ($query) {
                $currentDateTime = now();
                $query->whereDate('date', '>=', $currentDateTime->toDateString())
                    ->orWhere(function ($query) use ($currentDateTime) {
                        $query->whereDate('date', $currentDateTime->toDateString())
                            ->whereTime('time', '>', $currentDateTime->toTimeString());
                    });
            });

        if($withoutReturns){
            $futureSchedules->where('is_back_service', 'no')
            ->where('associated_schedule', 0);
        };

        $futureSchedules = $futureSchedules->orderByRaw("date ASC, time ASC")->get();
    
        return $futureSchedules;
    }
}
