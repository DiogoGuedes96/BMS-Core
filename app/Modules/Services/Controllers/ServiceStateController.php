<?php

namespace App\Modules\Services\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Services\Requests\ServiceStateRequest;
use App\Modules\Services\Requests\ServiceStateChangeDefaultRequest;
use App\Modules\Services\Resources\ServiceStateResource;
use App\Modules\Services\Resources\ServiceStateCollection;
use App\Modules\Services\Services\ServiceStateService;

class ServiceStateController extends Controller
{
    public function __construct(
        private ServiceStateService $serviceStateService
    )
    {
    }

    public function index(Request $request)
    {
        $serviceStates = $this->serviceStateService->getAll($request);

        return (new ServiceStateCollection($serviceStates))
			->response()->setStatusCode(200);
    }

    public function store(ServiceStateRequest $request)
    {
        if (!$serviceStateInTrash = $this->serviceStateService->getByNameInTrash($request->name)) {
            $result = $this->serviceStateService->create($request->all());

            if (!$result->success) {
                return response()->json([
                    'message' => $result->content,
                ], 400);
            }

            return (new ServiceStateResource($result->content))
    		    ->response()->setStatusCode(201);
        } else {
            $serviceStateRestored = $this->serviceStateService->restoreFromTrash(
                $serviceStateInTrash,
                $request->all()
            );

            return (new ServiceStateResource($serviceStateRestored))
    		    ->response()->setStatusCode(201);
        }
    }

    public function show($id)
    {
        if (!$serviceState = $this->serviceStateService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new ServiceStateResource($serviceState))
			->response()->setStatusCode(200);
    }

    public function update(ServiceStateRequest $request, $id)
    {
        if (!$serviceState = $this->serviceStateService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->serviceStateService->update($serviceState, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new ServiceStateResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function updateDefault(ServiceStateChangeDefaultRequest $request, $id)
    {
        if (!$serviceState = $this->serviceStateService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if ((bool) $request->is_default === true && $serviceState->is_default !== true) {
            $this->serviceStateService->resetDefault();
        }

        $result = $this->serviceStateService->update($serviceState, $request->only('is_default'));

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([
            'message' => 'Estado padrão editado com sucesso.'
        ], 200);
    }

    public function destroy($id)
    {
        if (!$serviceState = $this->serviceStateService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if ($serviceState->readonly) {
            return response()->json([
                'message' => 'O estado de serviço "' . $serviceState->name . '" não pode ser excluído.'
            ], 400);
        }

        $result = $this->serviceStateService->delete($serviceState);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
