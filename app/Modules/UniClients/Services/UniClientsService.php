<?php

namespace App\Modules\UniClients\Services;

use App\Modules\ActiveCampaign\Services\ActiveCampaignService;
use App\Modules\Notification\Models\Notifications;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\UniClients\Models\ReferrerChangeRequest;
use App\Modules\UniClients\Models\UniClients;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

class UniClientsService
{
    /** @var UniClients */
    private $model;

    /** @var ReferrerChangeRequest */
    private $referrerChangeRequest;

    /** @var NotificationService */
    private $notificationService;

    /** @var ActiveCampaignService */
    private $acService;

    public function __construct()
    {
        $this->model = new UniClients();
        $this->referrerChangeRequest = new ReferrerChangeRequest();
        $this->notificationService = new NotificationService();
        $this->acService = new ActiveCampaignService();
    }

    public function list($query, $userId)
    {
        $model = $this->model;

        if (!empty($query['search'])) {
            $model = $model->where('name', 'like', "%{$query['search']}%")
                ->orWhere('email', 'like', "%{$query['search']}%");
        }

        if (!empty($query['sort']) && !empty($query['order'])) {
            $model = $model->orderBy($query['sort'], $query['order']);
        }

        $user = User::where('id', $userId)->with('profile')->first();

        if ($user->profile->role !== 'admin') {
            $model = $model->where('referencer', $userId);
        }

        return !empty($query['per_page']) ? $model->with('referrer')
            ->paginate($query['per_page'] ?? 10) : $model->active()->with('referrer')->get();
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function delete(int $id)
    {
        return $this->model->find($id)->delete();
    }

    public function edit(int $id, $data)
    {
        return $this->model->find($id)->update($data);
    }

    public function show(int $id)
    {
        return $this->model->where('id', $id)->with('referrer')->first();
    }

    public function showOnlyBusinessOpen(int $id)
    {
        return $this->model->where('id', $id)->with('referrer')->first();
    }

    public function checkEmail(string $email)
    {
        $alreadyEmailInAC = null;
        try {
            $alreadyEmailInAC = $this->acService->searchContact($email);
        } catch (\Throwable $th) {
            //throw $th;
        }
        $alreadyEmailInUNI = $this->model->where('email', $email)->with('referrer')->first();

        return [
            'ac' => $alreadyEmailInAC,
            'uni' => $alreadyEmailInUNI
        ];
    }

    public function requestChangeReferrer(int $id, $data)
    {
        $client = $this->model->find($id);

        $hasNotification = Notifications::with(['notifiable'])->whereHas('notifiable', function ($query) use ($client) {
            $query->where('referrer_id', $client->referencer)
                ->where('client_id', $client->id)
                ->where('status', 'pending');
        })->where('message', 'referrer_change_request')->first();
        if (!$hasNotification) {
            $request = $this->referrerChangeRequest->create([
                'referrer_id' => $client->referencer,
                'client_id' => $client->id,
                'reason' => 'Request to change referrer',
                'requested_by' => $data['referrer'],
            ]);


            $this->notificationService->addNotification(
                $client->referencer,
                'Há uma solicitação de alteração de referenciador para o cliente ' . $client->name,
                'referrer_change_request',
                $request,
                $request->id
            );

            return $request;
        }

        return false;
    }

    public function requestAccepted(int $requestId, int $notificationId, int $userId)
    {
        $request = $this->referrerChangeRequest->find($requestId);

        $client = $this->model->find($request->client_id);
        $client->referencer = $request->requested_by;
        $client->save();

        $request->status = 'accepted';
        $request->approved_by = $userId;
        $request->save();

        $this->notificationService->changeStatus($notificationId, 'accepted');

        $this->notificationService->addNotification(
            $request->requested_by,
            'Sua solicitação de alteração de referenciador para o cliente ' . $client->name . ' foi aceita.',
            'referrer_change_request_accepted',
            $request,
            $request->id
        );

        return $request;
    }

    public function requestRejected(int $id, int $notificationId, int $userId)
    {
        $request = $this->referrerChangeRequest->find($id);

        $request->status = 'rejected';
        $request->approved_by = $userId;
        $request->save();

        $this->notificationService->changeStatus($notificationId, 'rejected');

        $this->notificationService->addNotification(
            $request->requested_by,
            'Sua solicitação de alteração de referenciador para o cliente ' . $request->client->name . ' foi rejeitada.',
            'referrer_change_request_rejected',
            $request,
            $request->id
        );

        return $request;
    }

    public function hardDelete(int $id)
    {
        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Delete the client using a raw SQL query
            DB::table('uni_clients')->where('id', $id)->delete();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $e) {
            throw new \Exception('Houve um erro ao tentar excluir permanentemente o registro.');
        }
    }

    public function getNotificationChangeReferrerList()
    {
        return Notifications::with(['notifiable'])->whereHas('notifiable', function ($query) {
            $query->where('status', 'pending')->where('referrer_id', auth()->user()->id);
        })->where('message', 'referrer_change_request')->get();
    }
}
