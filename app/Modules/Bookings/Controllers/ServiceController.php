<?php

namespace App\Modules\Bookings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Bookings\Requests\BookingServiceRequest;
use App\Modules\Bookings\Requests\BookingServiceColumnsRequest;
use App\Modules\Bookings\Requests\SendTimetableRequest;
use App\Modules\Bookings\Resources\BookingServiceResource;
use App\Modules\Bookings\Resources\BookingServiceCollection;
use App\Modules\Bookings\Resources\BookingServiceColumnsResource;
use App\Modules\Bookings\Services\BookingServiceService;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use App\Modules\Bookings\Models\BookingService;
use App\Modules\Bookings\Requests\BookingServicePaymentStatusRequest;
use App\Modules\Users\Services\UsersService;

use Illuminate\Support\Facades\Mail;
use App\Modules\Bookings\Mail\ShareTimetable;

class ServiceController extends Controller
{
    public function __construct(
        private BookingServiceService $bookingServiceService,
        private UsersService $usersService
    )
    {
    }

    public function index(Request $request)
    {
        $bookingServices = $this->bookingServiceService->getAll($request);

        return (new BookingServiceCollection($bookingServices))
			->response()->setStatusCode(200);
    }

    public function indexTrashed(Request $request)
    {
        $request['onlyTrashed'] = true;

        $bookingServices = $this->bookingServiceService->getAll($request);

        return (new BookingServiceCollection($bookingServices))
			->response()->setStatusCode(200);
    }

    public function store(BookingServiceRequest $request)
    {
        $result = $this->bookingServiceService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new BookingServiceResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function show($id)
    {
        if (!$bookingService = $this->bookingServiceService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        return (new BookingServiceResource($bookingService))
			->response()->setStatusCode(200);
    }

    public function update(BookingServiceRequest $request, $id)
    {
        if (!$bookingService = $this->bookingServiceService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        $result = $this->bookingServiceService->update($bookingService, $request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new BookingServiceResource($result->content))
    		->response()->setStatusCode(200);
    }

    public function destroy($id)
    {
        if (!$bookingService = $this->bookingServiceService->getById($id)) {
            return response()->json([
                'message' => 'Registro não encontrado',
            ], 404);
        }

        if (
            $bookingService->booking->status != StatusBookingEnum::DRAFT
            && $bookingService->booking->services->count() < 2
        ) {
            return response()->json([
                'message' => 'Não foi possível excluir o serviço pois uma reserva deve ter no mínimo um serviço.'
            ], 400);
        }

        $associatedData = [];

        if (!empty($associatedData)) {
            return response()->json([
                'message' => 'O serviço n° "'. $bookingService->id .'" não pode ser excluído, pois está vinculado a ' . implode(' e ', $associatedData) . '.'
            ], 400);
        }

        $result = $this->bookingServiceService->delete($bookingService);

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return response()->json([], 204);
    }

    public function columns(Request $request)
    {
        $user = $request->user();
        
        return (new BookingServiceColumnsResource($user))
        ->response()->setStatusCode(200);
    }
    
    public function updateColumns(BookingServiceColumnsRequest $request)
    {
        $user = $request->user();

        try {
            $settings = $user->settings ?? [];
            $settings['service_columns'] = $request->columns;

            unset($request['columns']);
            $request['settings'] = $settings;

            $user = $this->usersService->updateUser($request, $user->id);

            return response()->json([
                'message' => 'Service columns updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?? 400);
        }
    }

    public function sendTimetable(SendTimetableRequest $request)
    {
        if (!Mail::to($request->email)->send(
            new ShareTimetable(
                $request->conductor,
                $request->email,
                $request->date,
                $request->file('timetable')
            )
        )) {
            return response()->json([
                'message' => 'Não foi possível enviar a escala para o email de destino.'
            ], 400);
        }

        return response()->json([
            'message' => 'Escala enviada com sucesso.'
        ]);
    }
}
