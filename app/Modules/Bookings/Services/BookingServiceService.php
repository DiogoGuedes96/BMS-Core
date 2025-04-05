<?php

namespace App\Modules\Bookings\Services;

use App\Services\Service;
use Illuminate\Support\Facades\DB;
use App\Modules\Bookings\Models\BookingService;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use App\Modules\Bookings\Enums\ChargeEnum;
use App\Modules\Workers\Enums\TypeEnum as WorkerTypeEnum;

class BookingServiceService extends Service
{
    const SEARCHABLE_FIELDS = [
        'flight_number',
        'pickup_location',
        'pickup_zone',
        'pickup_address',
        'dropoff_location',
        'dropoff_zone',
        'dropoff_address',
        'notes',
        'internal_notes',
        'driver_notes'
    ];

    const FILTERABLE_FIELDS = [
        'car_type',
        'booking_id',
        'service_type_id',
        'service_state_id',
        'staff_id',
        'supplier_id',
        'operator_id',
        'vehicle_id',
        'pickup_location_id',
        'pickup_zone_id',
        'dropoff_location_id',
        'dropoff_zone_id',
        'was_paid',
    ];

    public function getAll($request)
    {
        $order = $request->order ?? 'created_at-desc';

        [$field, $direction] = explode('-', $order);
        
        $services = BookingService::orderBy($field, $direction);

        if ($request->has('onlyTrashed')) {
            $services->onlyTrashed();
        }

        if (!$request->filled('booking_id')) {
            $services->whereHas('booking', function($query) use ($request) {
                $query->where('status', '=', StatusBookingEnum::APPROVED);

                if ($request->has('onlyTrashed')) {
                    $query->onlyTrashed();
                }
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $services->where(function($query) use ($search, $request) {
                foreach(self::SEARCHABLE_FIELDS as $key => $field) {
                    if ($key > 0) {
                        $query->orWhere($field, 'like', '%'. $search .'%');
                    } else {
                        $query->where($field, 'like', '%'. $search .'%');
                    }
                }

                if ($request->filled('onlyWorkerType')) {
                    if ($request->onlyWorkerType == WorkerTypeEnum::SUPPLIERS) {
                        $query->orWhereHas('supplier', function($query) use ($search) {
                            $query->where('name', 'like', '%'. $search .'%');
                        });
                    }

                    if ($request->onlyWorkerType == WorkerTypeEnum::STAFF) {
                        $query->orWhereHas('staff', function($query) use ($search) {
                            $query->where('name', 'like', '%'. $search .'%');
                        });
                    }

                    $query->orWhereHas('booking', function($query) use ($search, $request) {
                        $query->where('client_name', 'like', '%'. $search .'%');
                    });
                }
            });
        }

        if ($request->has('emphasis')) {
            $bookings->where('emphasis', true);
        }

        if ($request->filled('start_date')) {
            $services->where('start', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $services->where('start', '<=', $request->end_date);
        }

        if ($request->has('withChild')) {
            $services->whereNull('parent_id');
        }

        if ($request->filled('onlyWorkerType')) {
            if ($request->onlyWorkerType == WorkerTypeEnum::SUPPLIERS) {
                $services->whereNotNull('supplier_id');
            }

            if ($request->onlyWorkerType == WorkerTypeEnum::STAFF) {
                $services->whereNotNull('staff_id');
            }
        }

        foreach(self::FILTERABLE_FIELDS as $key) {
            if ($request->filled($key)) {
                if ($key == 'service_type_id') {
                    $services->whereIn($key, explode(',', $request->{$key}));
                } else if ($key == 'was_paid') {
                    $services->where($key, (bool)$request->{$key});
                } else if ($key == 'operator_id') {
                    $services->whereHas('booking', function($query) use ($request) {
                        $query->where('operator_id', '=', $request->operator_id);
                    });
                } else {
                    $services->where($key, '=', $request->{$key});
                }
            }
        }

        $services = ($request->has('page'))
			? $services->paginate((int) $request->per_page ?? 10)
			: $services->get();

        return $services;
    }

    public function getById($id)
    {
        return BookingService::find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            unset($request['voucher']);
            unset($request['parent_id']);

            if (empty($request['emphasis'])) {
                $request['emphasis'] = false;
            }

            if (!$service = BookingService::create($request)) {
                throw new \Exception('Houve um erro ao tentar guardar o registro.');
            }

            DB::commit();

            return $this->result($service, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function update(BookingService $service, $request)
    {
        DB::beginTransaction();

        try {
            unset($request['voucher']);
            unset($request['parent_id']);

            if (empty($request['emphasis'])) {
                $request['emphasis'] = false;
            }

            if (!empty($request['hour'])) {
                $request['hour'] = substr($request['hour'], 0, 5) . ':00';
            }

            if (!$service->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($service, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }

    public function delete(BookingService $service)
    {
        DB::beginTransaction();

        try {
            if (!$service->delete()) {
                throw new \Exception('Houve um erro ao tentar excluir o registro.');
            }

            DB::commit();

            return $this->result('Registro removido com sucesso', true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }
}
