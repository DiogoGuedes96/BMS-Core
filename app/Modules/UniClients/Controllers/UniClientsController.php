<?php

namespace App\Modules\UniClients\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\UniClients\Collections\UniClientsCollection;
use App\Modules\UniClients\Requests\EditUniClientsRequest;
use App\Modules\UniClients\Requests\NewUniClientsRequest;
use App\Modules\UniClients\Resources\UniClientsResource;
use App\Modules\UniClients\Services\UniClientsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class UniClientsController extends Controller
{
    /** @var UniClientsService */
    private $service;
    public function __construct()
    {
        $this->service = new UniClientsService();
    }

    public function list(Request $request)
    {
        $user = $request->user();

        $clients = $this->service->list($request->query(), $user->id);

        return (new UniClientsCollection($clients))
            ->response()->setStatusCode(200);
    }

    public function store(NewUniClientsRequest $request)
    {
        $data = $request->all();
        try {
            $client = $this->service->store($data);

            return (new UniClientsResource($client))
                ->response()->setStatusCode(201);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao salvar novo cliente',
            ], 404);
        }
    }

    public function delete($id)
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'message' => 'Cliente deletado com sucesso',
            ], 200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao deletar cliente',
            ], 404);
        }
    }

    public function edit($id, EditUniClientsRequest $request)
    {
        try {
            $client = $this->service->edit($id, $request->all());

            return response()->json([
                'message' => 'Cliente editado com sucesso',
            ], 200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao editar cliente',
            ], 404);
        }
    }

    public function show($id)
    {
        try {
            $client = $this->service->show($id);

            return (new UniClientsResource($client))
                ->response()->setStatusCode(200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao buscar cliente',
            ], 404);
        }
    }

    public function showOnlyBusinessOpen($id)
    {
        try {
            $client = $this->service->show($id);
            $client->open = true;

            return (new UniClientsResource($client))
                ->response()->setStatusCode(200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao buscar cliente',
            ], 404);
        }
    }

    public function checkEmail($email)
    {
        try {
            $client = $this->service->checkEmail($email);

            if (empty($client['uni'])) {
                return response()->json([
                    'data' => !empty($client['ac']) ? $client['ac'][0] : null,
                    'message' => !empty($client['ac']) ?
                        'Os dados do contacto foram importados do Active Campaign' :
                        'Email disponível',
                ], 200);
            }

            return (new UniClientsResource($client['uni']))
                ->response()->setStatusCode(200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao consultar Email',
            ], 404);
        }
    }

    public function requestChangeReferrer(Request $request, $id)
    {
        try {
            $requestChange = $this->service->requestChangeReferrer($id, $request->all());

            if ($requestChange) {
                return response()->json([
                    'message' => 'Solicitação de alteração de referência enviada com sucesso',
                ], 200);
            }

            return response()->json(['message' => 'Não foi possível realizar a alteração de referenciador, pois já tem uma solitação ativa'], 400);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao solicitar alteração de referência',
            ], 404);
        }
    }

    public function requestAccepted(Request $request, $requestId, $notificationId)
    {
        try {
            $this->service->requestAccepted($requestId, $notificationId, $request->user()->id);

            return response()->json([
                'message' => 'Solicitação aceita com sucesso',
            ], 200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao aceitar solicitação',
            ], 404);
        }
    }

    public function requestRejected(Request $request, $requestId, $notificationId)
    {
        try {
            $this->service->requestRejected($requestId, $notificationId, $request->user()->id);

            return response()->json([
                'message' => 'Solicitação rejeitada com sucesso',
            ], 200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao rejeitar solicitação',
            ], 404);
        }
    }

    public function hardDelete(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->service->hardDelete($id);

            DB::commit();
            return response()->json([
                'message' => $id
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Erro ao deletar cliente',
            ], 404);
        }
    }

    public function getNotificationChangeReferrerList()
    {
        try {
            DB::beginTransaction();
            $notificationList = $this->service->getNotificationChangeReferrerList();
            DB::commit();
            return response()->json([
                'message' => 'list showed succedully',
                'data' => $notificationList
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao mostrar as notificação de trocar de refenciador',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
