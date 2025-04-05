<?php

namespace App\Modules\Routes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Routes\Requests\LocationRequest;
use App\Modules\Routes\Resources\LocationResource;
use App\Modules\Routes\Resources\LocationCollection;
use App\Modules\Routes\Services\LocationService;

class LocationController extends Controller
{
    public function __construct(
        private LocationService $locationService
    )
    {
    }

    public function index(Request $request)
    {
        $locations = $this->locationService->getAll($request);

        return (new LocationCollection($locations))
			->response()->setStatusCode(200);
    }

    public function store(LocationRequest $request)
    {
        $result = $this->locationService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new LocationResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show($id)
    {
        if (!$location = $this->locationService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new LocationResource($location))
			->response()->setStatusCode(200);
    }

    public function update(LocationRequest $request, $id)
    {
        if (!$location = $this->locationService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->locationService->update($location, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new LocationResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$location = $this->locationService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->locationService->delete($location);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
