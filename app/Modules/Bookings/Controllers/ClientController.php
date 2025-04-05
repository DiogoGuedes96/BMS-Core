<?php

namespace App\Modules\Bookings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Bookings\Requests\ClientRequest;
use App\Modules\Bookings\Resources\ClientResource;
use App\Modules\Bookings\Resources\ClientCollection;
use App\Modules\Bookings\Services\ClientService;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService
    )
    {
    }

    public function index(Request $request)
    {
        $clients = $this->clientService->getAll($request);

        return (new ClientCollection($clients))
			->response()->setStatusCode(200);
    }

    public function store(ClientRequest $request)
    {
        $result = $this->clientService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new ClientResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show($id)
    {
        if (!$client = $this->clientService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new ClientResource($client))
			->response()->setStatusCode(200);
    }

    public function update(ClientRequest $request, $id)
    {
        if (!$client = $this->clientService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->clientService->update($client, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new ClientResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$client = $this->clientService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->clientService->delete($client);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }
}
