<?php

namespace App\Modules\Workers\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Workers\Requests\WorkerRequest;
use App\Modules\Workers\Requests\WorkersListRequest;
use App\Modules\Workers\Requests\WorkerPasswordRequest;
use App\Modules\Workers\Resources\WorkerResource;
use App\Modules\Workers\Resources\WorkerCollection;
use App\Modules\Workers\Services\WorkerService;
use App\Modules\Users\Services\UsersService;

class WorkerController extends Controller
{
    public function __construct(
        private WorkerService $workerService,
        private UsersService $userService
    )
    {
    }

    public function index(WorkersListRequest $request)
    {
        $workers = $this->workerService->getAll($request);

        return (new WorkerCollection($workers))
			->response()->setStatusCode(200);
    }

    public function store(WorkerRequest $request)
    {
        $result = $this->workerService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new WorkerResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show($id)
    {
        if (!$worker = $this->workerService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new WorkerResource($worker))
			->response()->setStatusCode(200);
    }

    public function update(WorkerRequest $request, $id)
    {
        if (!$worker = $this->workerService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->workerService->update($worker, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new WorkerResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$worker = $this->workerService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->workerService->delete($worker);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
