<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** NotificationService */
    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function list(Request $request)
    {
        try {
            $notifications = $this->notificationService->listNotifications($request->user()->id);

            return response()->json([
                'message' => 'Listar de notificações',
                'data' => $notifications,
            ]);
        } catch (\Exception $e) {

            dd($e);
            return response()->json([
                'message' => 'Erro ao listar notificações',
                'data' => []
            ], 404);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $this->notificationService->markAllAsRead($request->user()->id);

            return response()->json([
                'message' => 'Notificações marcadas como lidas',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao marcar notificações como lidas',
            ], 404);
        }
    }

    public function requestedByUser(Request $request)
    {
        try {
            $data = $this->notificationService->getNotificationRequestedByUser($request->input('notification_id'));
            return response()->json([
                'message' => 'Notificação listada com sucesso',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar notificação'
            ], 404);
        }
    }
}
