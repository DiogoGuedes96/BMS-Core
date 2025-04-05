<?php

namespace App\Modules\Bookings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Bookings\Services\BookingServiceService;
use App\Modules\Bookings\Resources\WorkerReportResource;
use App\Modules\Bookings\Resources\WorkerReportCollection;
use App\Modules\Workers\Services\WorkerService;
use App\Modules\Workers\Enums\TypeEnum as WorkerTypeEnum;
use App\Modules\Workers\Requests\WorkersListRequest;

class ReportController extends Controller
{
    public function __construct(
        private BookingServiceService $bookingServiceService,
        private WorkerService $workerService
    )
    {
    }

    public function workers(WorkersListRequest $request)
    {
        $workers = $this->workerService->getAll($request);

        return (new WorkerReportCollection($workers))
			->response()->setStatusCode(200);
    }

    public function showWorker($id)
    {
        if (!$worker = $this->workerService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new WorkerReportResource($worker))
			->response()->setStatusCode(200);
    }
}
