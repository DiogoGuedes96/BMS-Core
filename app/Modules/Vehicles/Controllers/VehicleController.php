<?php

namespace App\Modules\Vehicles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Vehicles\Requests\VehicleRequest;
use App\Modules\Vehicles\Resources\VehicleResource;
use App\Modules\Vehicles\Resources\VehicleCollection;
use App\Modules\Vehicles\Services\VehicleService;

class VehicleController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService
    )
    {
    }

    public function index(Request $request)
    {
        $vehicles = $this->vehicleService->getAll($request);

        return (new VehicleCollection($vehicles))
			->response()->setStatusCode(200);
    }

    public function store(VehicleRequest $request)
    {
        $result = $this->vehicleService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new VehicleResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show($id)
    {
        if (!$vehicle = $this->vehicleService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new VehicleResource($vehicle))
			->response()->setStatusCode(200);
    }

    public function update(VehicleRequest $request, $id)
    {
        if (!$vehicle = $this->vehicleService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->vehicleService->update($vehicle, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new VehicleResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$vehicle = $this->vehicleService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $associatedData = [];

        if ($vehicle->staff->count() > 0) {
            $associatedData[] = 'staff';
        }

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'A viatura "'. $vehicle->license .'" não pode ser excluída, pois está vinculada a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->vehicleService->delete($vehicle);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
