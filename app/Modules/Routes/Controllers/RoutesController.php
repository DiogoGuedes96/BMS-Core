<?php

namespace App\Modules\Routes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Routes\Requests\RouteCreate;
use App\Modules\Routes\Requests\RouteUpdate;
use App\Modules\Routes\Resources\RoutesResource;
use App\Modules\Routes\Services\RoutesService;
use Illuminate\Http\Request;

class RoutesController extends Controller
{
    private $routesService;

    public function __construct(RoutesService $routesService)
    {
        $this->routesService = $routesService;
    }

    public function list(Request $request)
    {
        $routes = $this->routesService->all($request);

        return new RoutesResource($routes);
    }

    public function one(String $id)
    {
        $route = $this->routesService->get($id);
        return response()->json($route);
    }

    public function save(RouteCreate $request)
    {
        if (!$routeInTrash = $this->routesService->getByZonesInTrash($request->from_zone_id, $request->to_zone_id)) {
            $route = $this->routesService->create($request);
        } else {
            $route = $this->routesService->restoreFromTrash($routeInTrash);
        }

        return response()->json($route, 201);
    }

    public function edit(RouteUpdate $request, String $id)
    {
        $route = $this->routesService->edit($request, $id);

        return response()->json($route);
    }

    public function delete(String $id)
    {
        if (!$route = $this->routesService->get($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $associatedData = [];

        if ($route->tables->count() > 0) {
            $associatedData[] = 'tabelas';
        }

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'A rota "'. $route->fromZone->name .' - '. $route->toZone->name . '" não pode ser excluída, pois está vinculada a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->routesService->delete($route);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
