<?php

namespace App\Modules\Bookings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Bookings\Requests\BookingRequest;
use App\Modules\Bookings\Requests\BookingChangeStatusRequest;
use App\Modules\Bookings\Requests\BookingUpdateVoucherRequest;
use App\Modules\Bookings\Requests\BookingOperatorRequest;
use App\Modules\Bookings\Requests\SendVoucherRequest;
use App\Modules\Bookings\Requests\ForceDeleteRequest;
use App\Modules\Bookings\Resources\BookingResource;
use App\Modules\Bookings\Resources\BookingCollection;
use App\Modules\Bookings\Resources\BookingOperatorResource;
use App\Modules\Bookings\Resources\BookingOperatorCollection;
use App\Modules\Bookings\Services\BookingService;
use App\Modules\Bookings\Services\ClientService;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use App\Modules\Bookings\Enums\CreatedByEnum;

use Illuminate\Support\Facades\Mail;
use App\Modules\Bookings\Mail\ShareVoucher;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private ClientService $clientService
    )
    {
    }

    public function index(Request $request)
    {
        $bookings = $this->bookingService->getAll($request);

        if (
            ($request->filled('created_by') && $request['created_by'] == CreatedByEnum::ATRAVEL)
            || !$request->filled('created_by')
        ) {
            return (new BookingCollection($bookings))
                ->response()->setStatusCode(200);
        }

        return (new BookingOperatorCollection($bookings))
			->response()->setStatusCode(200);
    }

    public function indexTrashed(Request $request)
    {
        $request['onlyTrashed'] = true;

        $bookings = $this->bookingService->getAll($request);

        return (new BookingCollection($bookings))
			->response()->setStatusCode(200);
    }

    public function draft($id = null)
    {
        if (!$id || !$draft = $this->bookingService->getDraftById($id)) {
            $result = $this->bookingService->create([
                'status' => StatusBookingEnum::DRAFT
            ]);

            if (!$result->success) {
                return response()->json([
                    'message' => $result->content
                ], 400);
            }

            return (new BookingResource($result->content))
                ->response()->setStatusCode(201);
        }

        return (new BookingResource($draft))
			->response()->setStatusCode(200);
    }

    public function pending(BookingOperatorRequest $request)
    {
        $dataRequest = $request->all();
        $dataRequest['operator_id'] = $request->user()->worker->id;
        $dataRequest['status'] = StatusBookingEnum::PENDING;
        $dataRequest['created_by'] = CreatedByEnum::OPERATOR;

        $result = $this->bookingService->create($dataRequest);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new BookingResource($result->content))
            ->response()->setStatusCode(201);
    }

    public function store(BookingRequest $request, $id = null)
    {
        if ($request->filled('client_email')) {
            $clientId = $this->handleBookingClient($request);

            if (!empty($clientId)) {
                $request['booking_client_id'] = $clientId;
            }
        }

        $dataRequest = $request->except('status');
        $dataRequest['status'] = StatusBookingEnum::APPROVED;

        if (!$id || !$booking = $this->bookingService->getDraftById($id)) {
            $result = $this->bookingService->create($dataRequest);
        } else {
            $result = $this->bookingService->update($booking, $dataRequest);
        }

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new BookingResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show(Request $request, $id)
    {
        $request['onlyOnGetById'] = '1';

        if (!$booking = $this->bookingService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if (
            $booking->created_by == CreatedByEnum::OPERATOR
            && $booking->status != StatusBookingEnum::APPROVED
        ) {
            return (new BookingOperatorResource($booking))
                ->response()->setStatusCode(200);
        }

        return (new BookingResource($booking))
			->response()->setStatusCode(200);
    }

    public function showTrashed($id)
    {
        if (!$booking = $this->bookingService->getTrashedById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new BookingResource($booking))
			->response()->setStatusCode(200);
    }

    public function update(BookingRequest $request, $id)
    {
        if (!$booking = $this->bookingService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if ($request->filled('client_email') && $booking->client_email != $request->client_email) {
            $clientId = $this->handleBookingClient($request);

            $request['booking_client_id'] = $clientId;
        }

        $result = $this->bookingService->update($booking, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new BookingResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function updatePending(BookingOperatorRequest $request, $id)
    {
        if (!$booking = $this->bookingService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->bookingService->update($booking, $request->except('operator_id', 'status', 'created_by'));

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new BookingOperatorResource($result->content))
            ->response()->setStatusCode(200);
    }

    private function handleBookingClient($request)
    {
        $client = $this->clientService->getByEmail($request->client_email);

        if ($client) {
            $this->clientService->update($client, [
                'name' => $request->client_name,
                'phone' => $request->client_phone
            ]);

            return $client->id;
        } else if ($hasBookingClient = $this->bookingService->getByClientEmail($request->client_email)) {
            $result = $this->clientService->create([
                'name' => $request->client_name,
                'email' => $request->client_email,
                'phone' => $request->client_phone
            ]);
    
            if ($result->success) {
                return $result->content->id;
            }
        }

        return null;
    }

    public function updateStatus(BookingChangeStatusRequest $request, $id)
    {
        if (!$booking = $this->bookingService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->bookingService->update($booking, $request->only('status', 'status_reason'));

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 200);
    }

    public function updateVoucher(BookingUpdateVoucherRequest $request, $id)
    {
        if (!$booking = $this->bookingService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->bookingService->updateVoucher($booking, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([
            'message' => 'Voucher editado com sucesso.'
        ], 200);
    }

    public function destroy($id)
    {
        if (!$booking = $this->bookingService->getById($id)) {
            if (!$booking = $this->bookingService->getDraftById($id)) {
                return response()->json([
                    'message' => 'Registro não encontrado',
                ], 404);
            }
        }

        $associatedData = [];

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'A reserva n° "'. $booking->id .'" não pode ser excluída, pois está vinculada a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->bookingService->delete($booking);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }

    public function forceDestroy(ForceDeleteRequest $request)
    {
        $messages = [];

        foreach ($request->id as $id) {            
            if (!$booking = $this->bookingService->getTrashedById($id)) {
                $messages[] = 'A reserva de n° '. $id .' não foi encontrada.';

                continue;
            }

            $result = $this->bookingService->forceDelete($booking);

            if (!$result->success) {
                $messages[] = 'Erro ao apagar a reserva de n° '. $id .': ' . $result->content;
            }
        }

        if (!empty($messages)) {
            return response()->json(compact('messages'), 200);
        }

        return response()->json([], 204);
    }

    public function restore($id)
    {
        if (!$booking = $this->bookingService->getTrashedById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->bookingService->restore($booking);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([
            'message' => 'Registro restaurado com sucesso.'
        ], 200);
    }

    public function sendVoucher(SendVoucherRequest $request, $id)
    {
        if (!$booking = $this->bookingService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if (!Mail::to($request->email)->send(new ShareVoucher($booking, $request->file('voucher')))) {
            return response()->json([
                'message' => 'Não foi possível enviar o voucher para o email de destino.'
            ], 400);
        }

        return response()->json([
            'message' => 'Voucher enviado com sucesso.'
        ]);
    }
}
