<?php

namespace App\Modules\UniDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\UniDashboard\Requests\BoardNumbersRequest;
use App\Modules\UniDashboard\Services\UniDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UniDashboardController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new UniDashboardService();
    }

    public function getBoardNumbers(BoardNumbersRequest $request)
    {
        $client = $request->get('client', null);
        $startDate = $request->get('startDate', null);
        $endDate = $request->get('endDate', null);
        $type = $request->get('type', null);
        $referrer = $request->get('referrer', null);
        $businessCoach = $request->get('businessCoach', null);
        $closer = $request->get('closer', null);
        $user = $request->get('user', null);

        $leads = $this->service->getLeads($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer);
        $newClients = $this->service->getNewClients($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);
        $newBusiness = $this->service->getNewBusiness($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);
        $closedBusiness = $this->service->getClosedBusiness($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);
        $wonBusiness = $this->service->getWonBusiness($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);
        $loseBusiness = $this->service->getLoseBusiness($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);
        $receiveBusiness = $this->service->getToReceiveBusiness($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);
        $totalBusiness = $this->service->getNewBusinessByType($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user);

        $totalReceiveBusiness = number_format(array_sum(array_column($this->getCurrentMonth($receiveBusiness), 'values')), 2);
        $countTotalBusiness = (int)array_sum(array_column($totalBusiness, 'values'));


        return response()->json([
            'message' => 'This is an example response from the UniDashboard module.',
            'data' => [
                'leads' => $leads,
                'newClients' => $newClients,
                'newBusiness' => $newBusiness,
                'closedBusiness' => $closedBusiness,
                'wonBusiness' => $wonBusiness,
                'loseBusiness' => $loseBusiness,
                'totalReceive' => $totalReceiveBusiness,
                'totalBusiness' => $countTotalBusiness
            ]
        ]);
    }

    public function getCurrentMonth($array)
    {
        Carbon::setLocale('pt_BR');
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->translatedFormat('F');

        $totalPendent = [];

        foreach ($array as $key => $element) {
            if ($element['month'] == $currentMonth) {
                array_push($totalPendent, $element);
            }
        }

        return $totalPendent;
    }

    public function getToReceiveBusiness(BoardNumbersRequest $request)
    {
        $client = $request->get('client', null);
        $startDate = $request->get('startDate', null);
        $endDate = $request->get('endDate', null);
        $type = $request->get('type', null);
        $referrer = $request->get('referrer', null);
        $businessCoach = $request->get('businessCoach', null);
        $closer = $request->get('closer', null);
        $user = $request->get('user', null);

        return response()->json([
            'message' => 'This is an example response from the UniDashboard module.',
            'data' => $this->service->getToReceiveBusiness($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user)
        ]);
    }

    public function getNewBusinessByType(BoardNumbersRequest $request)
    {
        $client = $request->get('client', null);
        $startDate = $request->get('startDate', null);
        $endDate = $request->get('endDate', null);
        $type = $request->get('type', null);
        $referrer = $request->get('referrer', null);
        $businessCoach = $request->get('businessCoach', null);
        $closer = $request->get('closer', null);
        $user = $request->get('user', null);

        return response()->json([
            'message' => 'This is an example response from the UniDashboard module.',
            'data' => $this->service->getNewBusinessByType($type, $client, $startDate, $endDate, $referrer, $businessCoach, $closer, $user)
        ]);
    }

    public function getHistoryByKanban(BoardNumbersRequest $request, $kanbanId)
    {
        $startDate = $request->get('startDate', null);
        $endDate = $request->get('endDate', null);
        $user = $request->get('user', null);

        return response()->json([
            'message' => 'This is an example response from the UniDashboard module.',
            'data' => $this->service->getHistoryByKanban($kanbanId, $startDate, $endDate, $user)
        ]);
    }
}
