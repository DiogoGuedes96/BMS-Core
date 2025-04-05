<?php

namespace App\Modules\Schedule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Schedule\Services\BmsScheduleEventRememberService;
use Exception;
use Illuminate\Support\Facades\Auth;

class BmsScheduleEventRememberController extends Controller
{
    private $bmsSchduleEventRememberService;

    public function __construct()
    {
       $this->bmsSchduleEventRememberService = new BmsScheduleEventRememberService();
    }

    public function listCurrentMinuteBmsScheduleEventsRemembers($onlyRead = false)
    {
        $onlyRead = filter_var($onlyRead, FILTER_VALIDATE_BOOLEAN);

        try {
            $user = Auth::user();

            $eventRemembers = $this->bmsSchduleEventRememberService->getCurrentMinuteEventRemembersFromUser($user->id, $onlyRead);

            return response()->json(['remembers' => $eventRemembers]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setDone($eventId)
    {
        try {
            $this->bmsSchduleEventRememberService->setDone($eventId);

            return response()->json(['message' => 'done']);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
