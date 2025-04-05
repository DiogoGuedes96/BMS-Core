<?php

namespace App\Modules\Schedule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Schedule\Requests\CreateBmsEventReminderRequest;
use App\Modules\Schedule\Services\BmsScheduleEventRememberService;
use App\Modules\Schedule\Services\BmsScheduleEventReminderService;
use App\Modules\Schedule\Services\BmsScheduleEventService;
use Exception;

class BmsScheduleEventReminderController extends Controller
{
    private $bmsScheduleEventReminderService;
    private $bmsScheduleEventRememberService;
    private $bmsScheduleEventService;

    public function __construct()
    {
        $this->bmsScheduleEventReminderService = new BmsScheduleEventReminderService();
        $this->bmsScheduleEventRememberService = new BmsScheduleEventRememberService();
        $this->bmsScheduleEventService = new BmsScheduleEventService();
    }

    public function createEventReminder(CreateBmsEventReminderRequest $createBmsEventReminderRequest) {
        try{
            $reminder = $this->bmsScheduleEventReminderService->createBmsScheduleEventReminder($createBmsEventReminderRequest->reminder);

            $dates = $this->bmsScheduleEventService->findNextDaysToNewEvent($reminder);
            $this->bmsScheduleEventService->generateEventsAndRemembers($reminder, $dates);

            return response()->json(['events' => $reminder]);
        }catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function editEventReminder(CreateBmsEventReminderRequest $editReminderRequest) {
        try {
            $reminder = $this->bmsScheduleEventReminderService->editBmsScheduleEventReminder($editReminderRequest->reminder);
            $dates = $this->bmsScheduleEventService->findNextDaysToNewEvent($reminder);

            $this->bmsScheduleEventService->generateEventsAndRemembers($reminder, $dates);

            return response()->json(['events' => $reminder]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function softDeleteBmsScheduleEvent($eventId, $type) {
        try {
            $this->bmsScheduleEventReminderService->removeReminder($eventId, $type);
            return response()->json(['events' => [], 'message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
