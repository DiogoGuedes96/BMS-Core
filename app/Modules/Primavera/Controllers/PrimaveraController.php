<?php

namespace App\Modules\Primavera\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Clients\Services\ClientsService;
use App\Modules\Primavera\Services\PrimaveraClientsService;
use Illuminate\Http\Request;

class PrimaveraController extends Controller
{
/**
     * @var PrimaveraClientsService
     */
    private $primaveraService;

    /**
     * @var ClientsService
     */
    private $clientsService;

    public function __construct()
    {
        $this->primaveraService = new PrimaveraClientsService();
        $this->clientsService = new ClientsService();
    }

    public function getClients(Request $request)
    {
        try {
            $data = $this->primaveraService->getAllClients();

            return response()->json([
                'message' => 'All clients from primavera.',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], $e->getCode()  ?? 500);
        };
    }

    public function getClientsByPhone(Request $request)
    {
        try {
            $data = $this->clientsService->getClientsByPhone($request->input('phones'));

            return response()->json([
                'message' => 'List clients from primavera by phone.',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], $e->getCode()  ?? 500);
        };
    }
}
