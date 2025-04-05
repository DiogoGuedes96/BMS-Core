<?php

namespace App\Modules\Tables\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Tables\Requests\TableRequest;
use App\Modules\Tables\Requests\TablesListRequest;
use App\Modules\Tables\Requests\TableSyncRoutesRequest;
use App\Modules\Tables\Resources\TableResource;
use App\Modules\Tables\Resources\TableCollection;
use App\Modules\Tables\Services\TableService;

class TableController extends Controller
{
    public function __construct(
        private TableService $tableService
    )
    {
    }

    public function index(TablesListRequest $request)
    {
        $tables = $this->tableService->getAll($request);

        return (new TableCollection($tables))
			->response()->setStatusCode(200);
    }

    public function store(TableRequest $request)
    {
        $result = $this->tableService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new TableResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show($id)
    {
        if (!$table = $this->tableService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new TableResource($table))
			->response()->setStatusCode(200);
    }

    public function update(TableRequest $request, $id)
    {
        if (!$table = $this->tableService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->tableService->update($table, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new TableResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$table = $this->tableService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $associatedData = [];

        if ($table->workers->count() > 0) {
            $associatedData[] = $table->getWorkerLabel();
        }

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'A tabela "'. $table->name .'" não pode ser excluída, pois está vinculada a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->tableService->delete($table);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }

    public function syncRoutes(TableSyncRoutesRequest $request, $id)
    {
        if (!$table = $this->tableService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $routes = [];

        foreach($request->routes as $route) {
            $routes[$route['id']] = [
                'pax14' => $route['pax14'],
                'pax58' => $route['pax58']
            ];
        }

        $result = $this->tableService->syncRoutes($table, $routes);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new TableResource($result->content))
    		->response()->setStatusCode(200);
    }
}
