<?php

namespace App\Modules\Clients\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Clients\Models\Clients;
use App\Modules\Clients\Requests\CreateClientRequest;
use App\Modules\Clients\Requests\EditClientRequest;
use App\Modules\Clients\Resources\ClientResource;
use App\Modules\Clients\Resources\ClientTotalResource;
use App\Modules\Clients\Services\ClientResponsibleService;
use App\Modules\Clients\Services\ClientsService;
use Illuminate\Http\Request;
use Exception;


class ClientsController extends Controller
{
    private $clientService;
    private $clientResponsibleService;

    public function __construct()
    {
        $this->clientService = new ClientsService();
        $this->clientResponsibleService = new ClientResponsibleService();
    }

    public function listAllClients(Request $request)
    {
        try {
            $search  = $request->get('search', null);
            $status  = $request->get('status', null) == "all" ? 'all' : $request->get('status', null);
            $type    = $request->get('type', null) == "all" ? null : $request->get('type', null);
            $sorter  = $request->get('sorter', null);
            $perPage = $request->get('perPage', 10);

            if ($search || $status === "0" || $status === "1" || $type || $sorter) {
                $status = $status === "0" || $status === "1" ? (int) $status : null;
                $data = $this->clientService->getFilteredClients($search, $status, $type, $sorter, $perPage);
                return (ClientResource::collection($data))->response()->setStatusCode(200);
            } else {
                $data = $this->clientService->getAllClients($perPage);
                return (ClientResource::collection($data))->response()->setStatusCode(200);
            }
        } catch (Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }

    public function createClient(CreateClientRequest $createClientRequest)
    {
        try {
            $this->clientService->createClient($createClientRequest->all());

            return response()->json([
                'message' => 'Client created successfully',
            ]);
        } catch (Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }

    public function editClient(EditClientRequest $editClientRequest)
    {
        try {
            $this->clientService->editClient($editClientRequest->all());

            return response()->json([
                'message' => 'Client eddited successfully',
            ]);
        } catch (Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }

    public function softDeleteClient($clientId)
    {
        try {
            $this->clientService->softDeleteClient($clientId);

            return response()->json([
                'message' => 'Client deleted successfully',
            ]);
        } catch (Exception $th) {
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }

    public function total()
    {
        $total = $this->clientService->getTotal();

        return (new ClientTotalResource($total))
            ->response()->setStatusCode(200);
    }

    public function getClientsFromResponsible($responsibleId)
    {
        try {
            $clients = $this->clientResponsibleService->getClientsFromResponsible($responsibleId);
            return (ClientResource::collection($clients))->response()->setStatusCode(200);
        } catch (Exception $th) {
            dd($th);
            return response()->json(['message' => 'error', 'code' => 'Try again later!'], 500);
        }
    }
}
