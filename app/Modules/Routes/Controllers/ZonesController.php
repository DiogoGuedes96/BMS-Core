<?php

namespace App\Modules\Routes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Routes\Requests\ZonesCreate;
use App\Modules\Routes\Requests\ZonesUpdate;
use App\Modules\Routes\Resources\AllZonesResource;
use App\Modules\Routes\Services\ZonesService;
use Illuminate\Http\Request;

class ZonesController extends Controller
{
    private $zonesService;

    public function __construct(ZonesService $zonesService)
    {
        $this->zonesService = $zonesService;
    }

    public function listAllPaged(Request $request)
    {
        $zones = $this->zonesService->paginate($request);

        return response()->json($zones);
    }

    public function all()
    {
        $zones = $this->zonesService->all();

        return new AllZonesResource($zones);
    }

    public function one(String $id)
    {
        $zone = $this->zonesService->get($id);
        return response()->json($zone);
    }

    public function save(ZonesCreate $request)
    {
        if (!$zoneInTrash = $this->zonesService->getByNameInTrash($request->name)) {
            $zone = $this->zonesService->create($request);
        } else {
            $zone = $this->zonesService->restoreFromTrash($zoneInTrash);
        }

        return response()->json($zone, 201);
    }

    public function edit(ZonesUpdate $request, String $id)
    {
        $zone = $this->zonesService->edit($request, $id);

        return response()->json($zone);
    }

    public function delete(String $id)
    {
        if (!$zone = $this->zonesService->get($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $associatedData = [];

        if ($zone->locations->count() > 0) {
            $associatedData[] = 'locais';
        }

        if ($zone->routesFromZone->count() > 0 || $zone->routesToZone->count() > 0) {
            $associatedData[] = 'rotas como zona de partida ou de destino';
        }

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'A zona "'. $zone->name . '" não pode ser excluída, pois está vinculada a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->zonesService->delete($zone);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
