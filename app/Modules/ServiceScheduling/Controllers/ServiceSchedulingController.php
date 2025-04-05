<?php

namespace App\Modules\ServiceScheduling\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;
use App\Modules\ServiceScheduling\Requests\ServiceSchedulingCanceledRequest;
use App\Modules\ServiceScheduling\Requests\ServiceSchedulingRequest;
use App\Modules\ServiceScheduling\Resources\ServiceSchedulingResource;
use App\Modules\ServiceScheduling\Resources\ServiceSchedulingTotalResource;
use App\Modules\ServiceScheduling\Services\ServiceSchedulingService;
use Illuminate\Http\Request;

class ServiceSchedulingController extends Controller
{
    /** @var ServiceSchedulingService */
    protected $serviceSchedulingService;

    public function __construct()
    {
        $this->serviceSchedulingService = new ServiceSchedulingService();
    }

    public function list(Request $request)
    {
        try {
            $services = $this->serviceSchedulingService->list($request);
            return (ServiceSchedulingResource::collection($services))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function total()
    {
        try {
            $total = $this->serviceSchedulingService->getTotal();

            return (new ServiceSchedulingTotalResource($total))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function newSchedule (ServiceSchedulingRequest $request) {
        try {
            $this->serviceSchedulingService->newSchedule($request->all());
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 400);
        }
    }

    public function editSchedule (ServiceSchedulingModel $schedule, ServiceSchedulingRequest $request) {
        try {
            $editSchedule = $this->serviceSchedulingService->editSchedule($schedule, $request->all());
            if (isset($editSchedule['error'])) {
                return response()->json(['message' => 'Something went wrong', 'error' => $editSchedule['error']], 400);
            }
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], $e->getCode() ?? 400);
        }
    }

    public function restoreCanceledSchedule(ServiceSchedulingModel $schedule) {
        try {
            $this->serviceSchedulingService->restoreCanceledSchedule($schedule);
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteSchedule(ServiceSchedulingCanceledRequest $schedule) {
        try {
            $this->serviceSchedulingService->deleteSchedule($schedule->all());
            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function getRepeatSchedulePosition (ServiceSchedulingModel $schedule) {
        try {

            return response()->json([
                'message' => 'Success',
                'data' => $this->serviceSchedulingService->getRepeatSchedulePosition($schedule)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function getSchedulesFromPatient($patientId, $withoutReturns = false){
        try {

            $schedules = $this->serviceSchedulingService->getSchedulesFromPatient($patientId, $withoutReturns);
            return (ServiceSchedulingResource::collection($schedules))->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 500);
        }
    }
}
