<?php

namespace App\Modules\Dashboard\Services;

use App\Modules\Calls\Models\AsteriskCall;
use App\Modules\Clients\Models\Clients;
use App\Modules\Orders\Models\Order;

class DashboardService
{
    private $wherePeriods;

    public function __construct()
    {
        $this->wherePeriods = [
            'now' => "writen_date >= '". date('Y-m-d') ." 00:00:00'",
            'week' => "writen_date >= '". date('Y-m-d', strtotime("-7 days")) ." 00:00:00'",
            'month' => "writen_date >= '". date('Y-m-d', strtotime("-30 days")) ." 00:00:00'",
            'last_week' => "writen_date BETWEEN '". date('Y-m-d', strtotime("-14 days")) ." 00:00:00' AND '". date('Y-m-d', strtotime("-7 days")) ." 00:00:00'",
            'last_month' => "writen_date BETWEEN '". date('Y-m-d', strtotime("-60 days")) ." 00:00:00' AND '". date('Y-m-d', strtotime("-30 days")) ." 00:00:00'"
        ];
    }

    public function getInvoicesKpis($period = 'now')
    {
        $wherePeriod = str_replace(' 00:00:00', '', $this->wherePeriods[$period]);

        $kpis = [
            'received' => Order::whereRaw($wherePeriod)
                ->where('status', '=', 'completed')->sum('total_value'),
            'to_receive' => Order::whereRaw($wherePeriod)
                ->whereNotIn('status', ['completed', 'canceled'])->sum('total_value'),
            'pending' => Order::whereRaw($wherePeriod)
                ->whereNotIn('status', ['completed', 'canceled'])->sum('total_value')
        ];

        if ($period != 'now') {
            $wherePeriod = str_replace(' 00:00:00', '', $this->wherePeriods['last_' . $period]);

            $kpis['last_received'] = Order::whereRaw($wherePeriod)
                ->where('status', '=', 'completed')->sum('total_value');

            $kpis['last_to_receive'] = Order::whereRaw($wherePeriod)
                ->whereNotIn('status', ['completed', 'canceled'])->sum('total_value');

            $kpis['last_pending'] = Order::whereRaw($wherePeriod)
                ->whereNotIn('status', ['completed', 'canceled'])->sum('total_value');
        }

        return $kpis;
    }

    public function getClientsKpis($period = 'now')
    {
        return [
            'total' => Clients::where('status', '!=', 'INACTIVO')->count()
        ];
    }

    public function getCallsKpis($period = 'now')
    {
        $wherePeriod = str_replace('writen_date', 'created_at', $this->wherePeriods[$period]);

        return [
            'lost' => AsteriskCall::whereRaw($wherePeriod)
                ->where('status', '=', 'missed')->count(),
            'hangup' => AsteriskCall::whereRaw($wherePeriod)
                ->where('status', '=', 'hangup')->count(),
            'total' => AsteriskCall::whereRaw($wherePeriod)->count()
        ];
    }

    public function getOrdersKpis($period = 'now')
    {
        $wherePeriod = str_replace(' 00:00:00', '', $this->wherePeriods[$period]);

        return [
            'by_calls' => Order::whereRaw($wherePeriod)
                ->whereNull('erp_invoice_id')->count(),
            'pending' => Order::whereRaw($wherePeriod)
                ->whereNotIn('status', ['completed', 'canceled'])->count(),
            'completed' => Order::whereRaw($wherePeriod)
                ->where('status', '=', 'completed')->count()
        ];
    }
}
