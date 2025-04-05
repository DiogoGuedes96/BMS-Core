<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Services\Requests\ServiceRequest;
use App\Modules\Services\Resources\ServiceResource;
use App\Modules\Services\Resources\ServiceCollection;
use App\Modules\Services\Services\ServiceService;
use App\Modules\Services\Enums\ColorsEnum;

class ServiceController extends Controller
{
    public function __construct(
        private ServiceService $serviceService
    )
    {
    }

    public function index(Request $request)
    {
        $services = $this->serviceService->getAll($request);

        return (new ServiceCollection($services))
			->response()->setStatusCode(200);
    }

    public function store(ServiceRequest $request)
    {
        if (!$serviceInTrash = $this->serviceService->getByNameInTrash($request->name)) {
            $result = $this->serviceService->create($request->all());
            
            if (!$result->success) {
                return response()->json([
                    'message' => $result->content,
                ], 400);
            }

            return (new ServiceResource($result->content))
    		    ->response()->setStatusCode(201);
        } else {
            $serviceRestored = $this->serviceService->restoreFromTrash(
                $serviceInTrash,
                $request->all()
            );

            return (new ServiceResource($serviceRestored))
    		    ->response()->setStatusCode(201);
        }
    }

    public function show($id)
    {
        if (!$service = $this->serviceService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new ServiceResource($service))
			->response()->setStatusCode(200);
    }

    public function update(ServiceRequest $request, $id)
    {
        if (!$service = $this->serviceService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->serviceService->update($service, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new ServiceResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$service = $this->serviceService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->serviceService->delete($service);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }

    public function colors()
    {
        return response()->json([
            'data' => ColorsEnum::getAll()
        ]);
    }
}
