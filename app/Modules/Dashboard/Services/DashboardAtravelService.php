<?php

namespace App\Modules\Dashboard\Services;

use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Models\BookingService;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use App\Modules\Workers\Enums\TypeEnum as WorkerTypeEnum;

class DashboardAtravelService
{
    public function kpis($request)
    {
        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');

        return [
            'closed' => BookingService::where('start', '=', $today->format('Y-m-d'))
                ->whereHas('serviceState', function($query) {
                    $query->where('name', '=', 'Fechado');
                })
                ->whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })
                ->count(),
            'approved' => BookingService::where('start', '=', $today->format('Y-m-d'))
                ->whereHas('serviceState', function($query) {
                    $query->where('name', '=', 'Aceite');
                })
                ->whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })->count(),
            'pending' => BookingService::where('start', '=', $today->format('Y-m-d'))
                ->whereHas('serviceState', function($query) {
                    $query->where('name', '=', 'Pendente');
                })
                ->whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })->count(),
            'unassigned' => BookingService::where('start', '=', $today->format('Y-m-d'))
                ->whereNull('supplier_id')
                ->whereNull('staff_id')
                ->whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })
                ->count(),
            'today' => BookingService::where('start', '=', $today->format('Y-m-d'))
                ->whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })
                ->count(),
            'tomorrow' => BookingService::where('start', '=', $tomorrow->format('Y-m-d'))
                ->whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })
                ->count(),
            'total' => BookingService::whereHas('booking', function($query) {
                    $query->where('status', '=', StatusBookingEnum::APPROVED);
                })->count()
        ];
    }
}
