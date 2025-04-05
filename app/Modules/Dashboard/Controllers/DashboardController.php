<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use App\Modules\Dashboard\Services\DashboardAtravelService;
use App\Modules\Bookings\Services\BookingService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $dashboardService;

    private $entities = [
        'invoices',
        'clients',
        'calls',
        'orders'
    ];

    private $services = [
        'ATRAVEL' => DashboardAtravelService::class
    ];

    public function __construct()
    {
        $this->dashboardService = new $this->services[config('app.bms_client')]();
    }

    public function getKpis(Request $request)
    {
        if (!in_array($request->entity, $this->entities)) {
            return response()->json(['message' => 'Entity not found'], 404);
        }

        return $this->dashboardService->{'get'. ucfirst($request->entity) .'Kpis'}($request->period ?? 'now');
    }

    public function kpis(Request $request) {
        $kpis = $this->dashboardService->kpis($request);

        return response()->json([
            'data' => $kpis
        ]);
    }
}
