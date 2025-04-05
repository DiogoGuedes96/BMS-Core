<?php

namespace App\Modules\Bookings\Services;

use App\Services\Service;
use Illuminate\Support\Facades\DB;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Models\BookingService;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use App\Modules\Workers\Enums\TypeEnum as WorkerTypeEnum;

class ReportService extends Service
{
    public function getResumeBookingsByOperator($request)
    {
        $bookings = Booking::select(
            'id', 'value',
            DB::raw("
                (CASE
                MONTHNAME(start_date)
                WHEN 'January' THEN 'Janeiro'
                WHEN 'February' THEN 'Fevereiro'
                WHEN 'March' THEN 'Março'
                WHEN 'April' THEN 'Abril'
                WHEN 'May' THEN 'Maio'
                WHEN 'June' THEN 'Junho'
                WHEN 'July' THEN 'Julho'
                WHEN 'August' THEN 'Agosto'
                WHEN 'September' THEN 'Setembro'
                WHEN 'October' THEN 'Outubro'
                WHEN 'November' THEN 'Novembro'
                WHEN 'December' THEN 'Dezembro'
                END) as month
            "))
            ->where('status', '=', StatusBookingEnum::APPROVED)
            ->where('operator_id', '=', $request->workerId);

        if ($request->filled('start_date')) {
            $bookings->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $bookings->where('start_date', '<=', $request->end_date);
        }

        if ($request->filled('year')) {
            $bookings->where(DB::raw('YEAR(start_date)'), '=', $request->year);
        }

        return $bookings->get();
    }

    public function getResumeServicesByWorker($request)
    {
        $services = BookingService::select(
            'id', 'value', 
            DB::raw("
                (CASE
                MONTHNAME(start)
                WHEN 'January' THEN 'Janeiro'
                WHEN 'February' THEN 'Fevereiro'
                WHEN 'March' THEN 'Março'
                WHEN 'April' THEN 'Abril'
                WHEN 'May' THEN 'Maio'
                WHEN 'June' THEN 'Junho'
                WHEN 'July' THEN 'Julho'
                WHEN 'August' THEN 'Agosto'
                WHEN 'September' THEN 'Setembro'
                WHEN 'October' THEN 'Outubro'
                WHEN 'November' THEN 'Novembro'
                WHEN 'December' THEN 'Dezembro'
                END) as month
            "))
            ->whereHas('booking', function($query) use ($request) {
                $query->where('status', '=', StatusBookingEnum::APPROVED);

                if ($request->workerType == WorkerTypeEnum::OPERATORS) {
                    $query->where('operator_id', '=', $request->workerId);
                }
            });

        if ($request->filled('start_date')) {
            $services->where('start', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $services->where('start', '<=', $request->end_date);
        }

        if ($request->filled('year')) {
            $services->where(DB::raw('YEAR(start)'), '=', $request->year);
        }

        if ($request->workerType == WorkerTypeEnum::SUPPLIERS) {
            $services->whereNotNull('supplier_id')
                ->where('supplier_id', '=', $request->workerId);
        } else if ($request->workerType == WorkerTypeEnum::STAFF) {
            $services->whereNotNull('staff_id')
                ->where('staff_id', '=', $request->workerId);
        }

        return $services->get();
    }

    public function getResumeServicesValueFromTable($request, $worker_type)
    {
        $subServices = BookingService::select(
            'bms_booking_services.id',
            'bms_booking_services.number_adults',
            'bms_booking_services.number_children',
            'bms_table_routes.pax14',
            'bms_table_routes.pax58',
            DB::raw("
                (CASE
                MONTHNAME(bms_booking_services.start)
                WHEN 'January' THEN 'Janeiro'
                WHEN 'February' THEN 'Fevereiro'
                WHEN 'March' THEN 'Março'
                WHEN 'April' THEN 'Abril'
                WHEN 'May' THEN 'Maio'
                WHEN 'June' THEN 'Junho'
                WHEN 'July' THEN 'Julho'
                WHEN 'August' THEN 'Agosto'
                WHEN 'September' THEN 'Setembro'
                WHEN 'October' THEN 'Outubro'
                WHEN 'November' THEN 'Novembro'
                WHEN 'December' THEN 'Dezembro'
                END) as month
            "))
        ->leftJoin('bms_workers', 'bms_workers.id', '=', 'bms_booking_services.'. $worker_type .'_id')
        ->leftJoin('bms_tables', 'bms_tables.id', '=', 'bms_workers.table_id')
        ->leftJoin('bms_table_routes', 'bms_table_routes.table_id', '=', 'bms_tables.id')
        ->leftJoin('bms_routes', 'bms_routes.id', '=', 'bms_table_routes.route_id')
        ->whereHas('booking', function($query) {
            $query->where('status', '=', StatusBookingEnum::APPROVED);
        })
        ->whereNotNull($worker_type .'_id')
        ->where($worker_type .'_id', '=', $request->workerId)
        ->whereRaw('bms_routes.from_zone_id = bms_booking_services.pickup_zone_id')
        ->whereRaw('bms_routes.to_zone_id = bms_booking_services.dropoff_zone_id');

        if ($request->filled('start_date')) {
            $subServices->where('bms_booking_services.start', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $subServices->where('bms_booking_services.start', '<=', $request->end_date);
        }

        if ($request->filled('year')) {
            $subServices->where(DB::raw('YEAR(bms_booking_services.start)'), '=', $request->year);
        }

        $services = DB::table(DB::raw("({$subServices->toSql()}) as result"))
            ->select(
                'id',
                DB::raw('(CASE WHEN (number_adults + number_children) < 5 then pax14 ELSE pax58 END) AS value'),
                'month'
            )
            ->mergeBindings($subServices->getQuery());

        return $services->get();
    }
}
