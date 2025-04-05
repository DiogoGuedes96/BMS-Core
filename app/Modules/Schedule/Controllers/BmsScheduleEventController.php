<?php

namespace App\Modules\Schedule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Schedule\Requests\ListEventsByDatesFromUserRequest;
use App\Modules\Schedule\Services\BmsScheduleEventService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BmsScheduleEventController extends Controller
{
    private $bmsSchduleEventService;

    public function __construct()
    {
       $this->bmsSchduleEventService = new BmsScheduleEventService();
    }

    public function listEventsByDatesFromUser(ListEventsByDatesFromUserRequest $listEventsByDatesFromUserRequest)
    {
        try {
            $user = Auth::user();

            if (!empty($listEventsByDatesFromUserRequest->dates)) {
                $dates = $listEventsByDatesFromUserRequest->dates;
            } else {
                $dates = $this->bmsSchduleEventService->getWeekDaysByDate(Carbon::now());
            }

            $scheduleEvents = $this->bmsSchduleEventService->getScheduleEventsByDatesFromUser($user->id, $dates);

            return response()->json(['events' => $scheduleEvents]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function delayBmsScheduleEvent(Request $request) {
        $event = $this->bmsSchduleEventService->addDelayToEvent($request->event_id, $request->delay);
        return response()->json(['events' => $event]);
    }
}
