<?php

namespace App\Modules\Bookings\Services;

use App\Services\Service;
use Illuminate\Support\Facades\DB;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use App\Modules\Bookings\Enums\CreatedByEnum;
use App\Modules\Workers\Enums\TypeEnum;

class BookingService extends Service
{
    const SEARCHABLE_FIELDS = [
        'id',
        'client_name',
        'reference'
    ];

    public function getAll($request)
    {
        $order = $request->order ?? 'start_date-desc';

        [$field, $direction] = explode('-', $order);

        $bookings = Booking::orderBy($field, $direction);

        if ($request->has('onlyTrashed')) {
            $bookings->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $bookings->where(function($query) use ($search) {
                foreach(self::SEARCHABLE_FIELDS as $key => $field) {
                    if ($key > 0) {
                        $query->orWhere($field, 'like', '%'. $search .'%');
                    } else {
                        $query->where($field, 'like', '%'. $search .'%');
                    }
                }

                $query->orWhereHas('operator', function($query) use ($search) {
                    $query->where('name', 'like', '%'. $search .'%');
                });
            });
        }

        if ($request->filled('start_date')) {
            if ($request->has('firstService')) {
                $bookings->whereHas('services', function($query) use ($request) {
                    $query->where('start', '>=', $request->start_date);
                });
            } else {
                $bookings->where('start_date', '>=', $request->start_date)
                    ->where('hour', '>=', '00:00');
            }
        }

        if ($request->filled('end_date')) {
            if ($request->has('firstService')) {
                $bookings->whereHas('services', function($query) use ($request) {
                    $query->where('start', '<=', $request->end_date);
                });
            } else {
                $bookings->where('start_date', '<=', $request->end_date)
                    ->where('hour', '<=', '23:59');
            }
        }

        if ($request->has('emphasis')) {
            $bookings->where('emphasis', true);
        }

        if ($request->has('was_paid')) {
            $bookings->where('was_paid', (bool) $request->was_paid);
        }

        if ($request->filled('booking_client_id')) {
            $bookings->where('booking_client_id', '=', $request->booking_client_id);
        }

        if ($request->filled('created_by')) {
            $bookings->where('created_by', '=', $request->created_by);

            if ($request->created_by == CreatedByEnum::OPERATOR) {
                $bookings->where('status', '!=', StatusBookingEnum::DRAFT);
                    
                if ($request->filled('status')) {
                    $bookings->where('status', '=', $request->status);
                }

                $user = $request->user();

                if(!empty($user->worker)
                    && $user->worker->type == TypeEnum::OPERATORS
                ) {
                    $bookings->where('operator_id', '=', $user->worker->id);
                }
            }

        } else if (
            !$request->filled('created_by') 
            || $request['created_by'] == CreatedByEnum::ATRAVEL
        ) {
            $bookings->where('status', '=', StatusBookingEnum::APPROVED);

            if ($request->filled('operator_id')) {
                $bookings->where('operator_id', '=', $request->operator_id);
            }
        }

        $bookings = ($request->has('page'))
			? $bookings->paginate((int) $request->per_page ?? 10)
			: $bookings->get();

        return $bookings;
    }

    public function getById($id)
    {
        return Booking::where('status', '!=', StatusBookingEnum::DRAFT)
            ->where('id', '=', $id)->first();
    }

    public function getDraft()
    {
        return Booking::where('status', '=', StatusBookingEnum::DRAFT)->first();
    }

    public function getDraftById($id)
    {
        return Booking::where('status', '=', StatusBookingEnum::DRAFT)
            ->where('id', '=', $id)->first();
    }

    public function getTrashedById($id)
    {
        return Booking::onlyTrashed()
            ->where('status', '=', StatusBookingEnum::APPROVED)
            ->where('id', '=', $id)->first();
    }

    public function getByClientEmail($email)
    {
        return Booking::where('status', '=', StatusBookingEnum::APPROVED)
            ->where('client_email', '=', $email)->first();
    }    

    public function create($request)
    {
        DB::beginTransaction();

        try {
            if (empty($request['deposits_paid'])) {
                $request['deposits_paid'] = 0;
            }

            if (empty($request['emphasis'])) {
                $request['emphasis'] = false;
            }

            if (empty($request['start_date'])) {
                $request['start_date'] = date('Y-m-d');
            }

            if (empty($request['hour'])) {
                $request['hour'] = date('H:i');
            }

            unset($request['voucher']);

            if (!$booking = Booking::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($booking, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(Booking $booking, $request)
    {
        DB::beginTransaction();

        try {
            if ($booking->status == StatusBookingEnum::DRAFT) {
                DB::table('bms_bookings')
                    ->where('id', '=', $booking->id)
                    ->update([
                        'start_date' => date('Y-m-d'),
                        'hour' => date('H:i')
                    ]);
            }

            if (empty($request['emphasis'])) {
                $request['emphasis'] = false;
            }

            unset($request['voucher']);

            if (!$booking->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($booking, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(Booking $booking)
    {
        DB::beginTransaction();

        try {
            if ($booking->status == StatusBookingEnum::DRAFT) {
                $booking->status = StatusBookingEnum::CANCELED;
                $booking->save();
            }

            if (!$booking->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso.', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function forceDelete(Booking $booking)
    {
        DB::beginTransaction();

        try {
            if (!$booking->forceDelete()) {
                throw new \Exception('Houve um erro ao tentar excluir permanentemente o registro.');
            }

            DB::commit();

            return $this->result('Registro removido permanentemente com sucesso.', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function restore(Booking $booking)
    {
        DB::beginTransaction();

        try {
            if (!$booking->restore()) {
                throw new \Exception('Houve um erro ao tentar restaurar o registro.');
            }

            DB::commit();

            return $this->result('Registro restaurado com sucesso.', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function updateVoucher(Booking $booking, $request)
    {
        DB::beginTransaction();

        try {
            $booking->voucher = [
                'company_name' => $request['company_name'],
                'company_email' => $request['company_email'],
                'company_phone' => $request['company_phone'],
                'client_name' => $request['client_name'],
                'pax_group' => $request['pax_group'],
                'operator' => $request['operator']
            ];

            $booking->save();

            $bookingServiceService = new BookingServiceService();

            foreach($request['services'] as $service) {
                if (!empty($service['id'])) {
                    if ($hasService = $bookingServiceService->getById($service['id'])) {
                        $hasService->voucher = [
                            'start' => $service['start'] ?? $hasService->start,
                            'hour' => $service['hour'] ?? $hasService->hour,
                            'pickup_location' => $service['pickup_location'] ?? $hasService->pickup_location,
                            'dropoff_location' => $service['dropoff_location'] ?? $hasService->dropoff_location,
                        ];

                        $hasService->save();
                    }
                }
            }

            DB::commit();

            return $this->result($booking, true);
        } catch(\Exception $error) {
            DB::rollback();
            return $this->result($error->getMessage(), false);
        }
    }

    public function syncServices(Booking $booking, $bookingServices)
    {
        try {
            $bookingServiceService = new BookingServiceService();

            foreach($bookingServices as $bookingService) {
                if (!empty($bookingService['id'])) {
                    if ($hasBookingService = $bookingServiceService->getById($bookingService['id'])) {   
                        $bookingServiceService->update($hasBookingService, $bookingService);
                    }
                } else {
                    $bookingServiceService->create($bookingService);
                }
            }

            return $this->result($booking, true);
        } catch(\Exception $error) {
            return $this->result($error->getMessage(), false);
        }
    }
}
