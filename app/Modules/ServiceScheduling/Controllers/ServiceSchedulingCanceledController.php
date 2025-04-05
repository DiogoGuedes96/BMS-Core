<?php

namespace App\Modules\ServiceScheduling\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;
use App\Modules\ServiceScheduling\Resources\ServiceSchedulingCanceledResource;
use App\Modules\ServiceScheduling\Services\ServiceSchedulingCanceledService;
use App\Modules\ServiceScheduling\Services\ServiceSchedulingService;
use Exception;
use Illuminate\Http\Request;

class ServiceSchedulingCanceledController extends Controller
{
    protected $serviceSchedulingService;
    protected $canceledService;
    public function __construct()
    {
        $this->serviceSchedulingService = new ServiceSchedulingService();
        $this->canceledService = new ServiceSchedulingCanceledService();
    }

    public function list(Request $request)
    {
        try {
            $services = $this->canceledService->list($request);
            return (ServiceSchedulingCanceledResource::collection($services))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getSchedulingByItem(ServiceSchedulingModel $schedule){
        try {
            $schedule_list = $this->canceledService->getSchedulingByItem($schedule);
            return response()->json([
                'message' => 'Success',
                'schedule' => $schedule_list
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 400);
        }
    }
}
