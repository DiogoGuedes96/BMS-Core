<?php

namespace App\Modules\ServiceScheduling\Services;

use App\Http\Services\BaseServices;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingCanceledModel;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingUploadsModel;
use DateTime;

class ServiceSchedulingCanceledService extends BaseServices
{
    private $serviceScheduling;
    private $serviceSchedulingUploads;
    private $serviceScheduleCanceled;
    public function __construct()
    {
        $this->serviceScheduling = new ServiceSchedulingModel();
        $this->serviceSchedulingUploads = new ServiceSchedulingUploadsModel();
        $this->serviceScheduleCanceled = new ServiceSchedulingCanceledModel();
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
            'canceled'
        ])->withTrashed()->where('bms_service_scheduling.deleted_at', '!=', null);
        
        if ($request->filled('sorter')) {
            $sorterKey = $request->filled('sorterKey') ? $request->input('sorterKey') : 'patient_name';
            $direction = $request->input('sorter') === 'ascend' ? 'asc' : 'desc';
        
            if ($sorterKey === 'patient_name') {
                $serviceModel->leftJoin('patients', 'bms_service_scheduling.patient_id', '=', 'patients.id')
                    ->orderBy('patients.name', $direction)
                    ->select('bms_service_scheduling.*');
            } else {
                $serviceModel->orderBy($sorterKey, $direction);
            }
        } else {
            $serviceModel->orderBy('bms_service_scheduling.date', 'desc');
        }

        if ($search) {
            $serviceModel = $serviceModel->where(function ($query) use ($search) {
                $query->whereHas('patient', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                              ->orWhere('patient_number', 'like', '%' . $search . '%');
                });
            });
        }

        if($searchStartDate || $searchEndDate) {
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

    public function getSchedulingByItem($scheduling) {
        $currentDate = new DateTime();
        return $this->serviceScheduling
            ->where('patient_id', $scheduling->patient_id)
            ->whereDate('date', '>', $currentDate->format('Y-m-d'))
            ->orWhere(function ($query) use ($currentDate) {
                $query->whereDate('date', $currentDate->format('Y-m-d'))
                    ->whereTime('time', '>=', $currentDate->format('H:i:s'));
            })->orderByRaw("date ASC, time ASC")
            ->get();
    }
}
