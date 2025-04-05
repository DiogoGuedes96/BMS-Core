<?php

namespace App\Modules\Calls\Services;

use App\Modules\Calls\Models\AsteriskCall;
use App\Modules\Calls\Models\AsteriskCredentials;
use App\Modules\Calls\Models\AsteriskEvent;
use App\Modules\Calls\Models\PhoneBlackList;
use App\Modules\Calls\Models\ViewAsteriskCallsWithEntities;
use App\Modules\Clients\Services\ClientsService;
use App\Modules\Patients\Services\PatientsService;
use Illuminate\Http\Request;
use \PAMI\Message\Event\EventMessage;
use Illuminate\Pagination\LengthAwarePaginator as PaginationClass;
use Throwable;
use Exception;


class AsteriskService
{
    private $asteriskCall;
    private $viewAsteriskCallsWithEntities;
    private $asteriskEvent;
    private $asteriskCredentials;

    public function __construct()
    {
        $this->viewAsteriskCallsWithEntities = new ViewAsteriskCallsWithEntities();
        $this->asteriskCall = new AsteriskCall();
        $this->asteriskEvent = new AsteriskEvent();
        $this->asteriskCredentials = new AsteriskCredentials();
    }

    /**
     * It saves the event to the database
     *
     * @param EventMessage event The name of the event.
     */
    public function saveAsteriskEvent(EventMessage $event)
    {
        $newEvent = [
            'uniqueid'      => strval($event->getKey('uniqueid')),
            'linkedid'      => strval($event->getKey('linkedid')),
            'type'          => strval($event->getKey('event')),
            'date'          => strval($event->getKey('event')),
            'channel'       => strval($event->getKey('channel')),
            'channel_state' => strval($event->getKey('channelState')),
            'event_json'    => $event->getRawContent()
        ];

        $this->asteriskEvent->create($newEvent);
    }

    /**
     * It Handles an asterisk Call
     * Saves or updates a call
     * Calls the function to save the event in the database
     *
     * @param EventMessage event The event name.
     */
    public function handleAsteriskCall(EventMessage $event, $command)
    {
        try {
            switch ($event->getName()) {
                case 'Newchannel':
                    if($event->getKey('Context') == 'from-internal') {
                        return false;
                    }

                    $newCall = [
                        'caller_phone' => strval($event->getKey('calleridnum')),
                        'linkedid'     => strval($event->getKey('linkedid')),
                        'status'       => 'ringing',
                        'callee_phone' => strlen(strval($event->getKey('Reg_calleenum'))) >= 1 ? strval($event->getKey('Reg_calleenum')) : null,
                        'client_name'  => strval($event->getKey('connectedlinename')),
                        'hangup_status' => 0
                    ];
                    $this->asteriskCall->create($newCall);

                    $this->saveAsteriskEvent($event);
                    $command->info(now()." ".$event->getName().  ": ". strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                     break;
                case 'Newstate':
                    if($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()){
                        $call->update(['status' => 'connected']);

                        if(strlen(strval($event->getKey('calleridnum'))) > 8){
                            $call->update(['caller_phone' => strval($event->getKey('calleridnum'))]);
                        }
                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now()." ".$event->getName().  ": ". strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                     break;
                case 'Hangup':
                    if($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()) {
                        if($call->status == 'ringing'){//If the call was ringing but never picked up (status = connected) then it was a missed call
                            $call->update(
                                [
                                    'status' => 'missed',
                                    'hangup_status' => $event->getKey('Cause')
                                ]
                            );
                        } elseif (in_array($call->status, ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603'])) {//If the call has one of this codes it was a missed call
                            $call->update(
                                [
                                    'status' => 'missed',
                                    'hangup_status' => $event->getKey('Cause')
                                ]
                            );
                        } else {
                            $call->update(
                                [
                                    'status' => 'hangup',
                                    'hangup_status' => $event->getKey('Cause')
                                ]
                            );

                            if(strlen(strval($event->getKey('calleridnum'))) > 8){
                                $call->update(
                                    [
                                        'caller_phone' => strval($event->getKey('calleridnum'))
                                    ]
                                );
                            }
                        }
                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now()." ".$event->getName().  ": ". strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                     break;
                case 'Dial':
                    if($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()) {
                        $call->update(['status' => 'Dial'] );

                        if(strlen(strval($event->getKey('calleridnum'))) > 8){
                            $call->update(['caller_phone' => strval($event->getKey('calleridnum'))]);
                        }

                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now()." ".$event->getName().  ": ". strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                     break;
                case 'Hold':
                    if($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()){
                        $call->update(['status' => 'Hold']);

                         if(strlen(strval($event->getKey('callee_phone'))) >= 1){
                            if(!$call->callee_number){
                                $call->update(['callee_phone' =>  strval($event->getKey('Reg_calleenum'))]);
                            }
                        }

                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now()." ".$event->getName().  ": ". strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                     break;
                default:
                    $call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first();
                    $this->updateCallePhoneNumber($call, $event, $command);

                    break;
            }
        } catch (Throwable $th){
            $command->error(now(). 'Error on saving event: ' . $th);
        }
    }

    public function updateCallePhoneNumber($call, $event, $command){
        try{
            if($call){
                if(strlen(strval($event->getKey('Reg_calleenum'))) >= 8){
                    if(!$call->callee_number){
                        $call->update(['callee_phone' =>  strval($event->getKey('Reg_calleenum'))]);
                    }
                }
            }
        }catch (Throwable $th){
            $command->error(now(). 'Error on saving event: ' . $th);
        }
    }

    /**
     * Get all calls in progress
     *
     * @return The calls in progress.
     */
    public function getCallsInProgress()
    {
        $blacklistPhones = PhoneBlackList::pluck('phone');

        $calls = $this->viewAsteriskCallsWithEntities
            ->whereIn('call_status', ['connected', 'ringing'])
            ->where(function ($query) use ($blacklistPhones) {
                $query->where('callee_phone', null)
                    ->orWhereNotIn('callee_phone', $blacklistPhones)
                    ->where('callee_phone', '!=', 'caller_phone');
            })->get();

        return $calls;
    }

    public function getOneCall($id)
    {
        $blacklistPhones = PhoneBlackList::pluck('phone');

        $callsQuery = $this->getCallsViewQuery(null, null, null, null);

        $call = $callsQuery->where('call_id', $id)->first();

        return $call;
    }

    /**
     * It ends all calls that are currently connected or ringing
     */
    public function endAllCalls()
    {
        $calls = $this->asteriskCall->whereIn('status', ['connected', 'ringing'])->get();

        foreach ($calls as $call){
            $call->update(['status' => 'hangup']);
        }
    }

    /**
     * It gets all the calls that are connected or hangup
     *
     * @return The calls that are connected or hangup.
     */
    public function getCallsHangup($request)
    {
        try {
            $search          = $request->get('search', null);
            $searchStartDate = $request->get('searchStartDate', null);
            $searchEndDate   = $request->get('searchEndDate', null);
            $callType        = 'hangup';

            $perPage   = $request->get('perPage', 10);
            $sorterKey = $request->get('fieldSorter', null);
            $sorterDirection = $request->get('sorter', null);

            $callsQuery = $this->getCallsViewQuery($search, $searchStartDate, $searchEndDate, $callType, $sorterKey, $sorterDirection);

            $calls = $callsQuery->paginate($perPage ?? 10);

            if ($calls instanceof PaginationClass) {
                $pagination = [
                    'current_page' => $calls->currentPage(),
                    'per_page'     => $calls->perPage(),
                    'total'        => $calls->total(),
                    'last_page'    => $calls->lastPage(),
                ];

                return ['calls' => $calls, 'meta' => $pagination];
            }

            return $calls;
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }

    public function exportCallsHangup($request){
        $search          = $request->get('search', null);
        $searchStartDate = $request->get('searchStartDate');
        $searchEndDate   = $request->get('searchEndDate');
        $callType        = 'hangup';

        $callsQuery = $this->getCallsViewQuery($search, $searchStartDate, $searchEndDate, $callType);

        $calls = $callsQuery->get();

        $imagePath = public_path('images/asmLogo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageSrc  = 'data:image/jpeg;base64,' . $imageData;

        $data = [
            'imageSrc'        => $imageSrc,
            'search '         => $search,
            'searchStartDate' => $searchStartDate,
            'searchEndDate'   => $searchEndDate,
            'calls'           => $calls->toArray(),
        ];

        return $data;
    }

    public function getCallsViewQuery($search, $searchStartDate, $searchEndDate, $callType, $sorterKey = null, $sorterDirection = null){
        $blacklistPhones = PhoneBlackList::pluck('phone');

        $callsQuery = $this->viewAsteriskCallsWithEntities;

            if ($callType == 'hangup') {
                $callStatusToIgnore = ['Hold', 'connected', 'ringing', 'missed'];
                $callsQuery = $callsQuery->whereNotIn('call_status', $callStatusToIgnore);
            }

            if ($callType == 'missed') {
                $callsQuery = $callsQuery->whereIn('call_status', ['missed'])
                    ->orWhereIn('call_hangup_status', ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603']);
            }

            if ($callType == 'inProgress') {
                $callsQuery = $callsQuery->whereIn('call_status', ['connected', 'ringing']);
            }
           
            $callsQuery = $callsQuery->where(function ($query) use ($blacklistPhones) {
                $query->where('callee_phone', null)
                    ->orWhereNotIn('callee_phone', $blacklistPhones)
                    ->where('callee_phone', '!=', 'caller_phone');
            });

            if ($searchStartDate || $searchEndDate) {
                $callsQuery = $callsQuery->where(function ($query) use ($searchStartDate, $searchEndDate) {
                    if ($searchStartDate && $searchEndDate) {
                        $query->whereBetween('call_created_at', [$searchStartDate, $searchEndDate]);
                    } elseif ($searchStartDate) {
                        $query->where('call_created_at', '>=', $searchStartDate);
                    } elseif ($searchEndDate) {
                        $query->where('call_created_at', '<=', $searchEndDate);
                    }
                });
            } else {
                if ($sorterKey && $sorterDirection) {
                    $sorterDirection = substr($sorterDirection, 0, -3);
                if ($sorterKey == 'date') {
                    $callsQuery = $callsQuery->orderby('call_created_at', $sorterDirection);
                }elseif ($sorterKey == 'name') {
                    $callsQuery = $callsQuery->orderBy('entity_name', $sorterDirection);
                }
            }else{
                $callsQuery = $callsQuery->orderby('call_created_at', 'desc');
            }    
        }

        if ($search) {
            $callsQuery = $callsQuery->where('entity_name', 'like', '%' . $search . '%');
        }

        return $callsQuery;
    }

    /**
     * It gets all the calls that were missed, and then it gets the clients
     *
     * Codes when cll is hangup without picking the phone
     * 17: User busy
     * 18: No user response
     * 19: No answer
     * 21: Call rejected
     * 22: Number changed
     * 32: No Circuit/Channel Available
     * 34: Circuit/channel congestion
     * 42: Switching equipment congestion
     * 480: Temporarily Unavailable
     * 487: Request Terminated
     * 600: Busy Everywhere
     * 603: Decline
     *
     * @return The function getCallsMissed() is returning the calls that were missed.
     */
    public function getCallsMissed($request)
    {
        try {
            $search          = $request->get('search', null);
            $searchStartDate = $request->get('searchStartDate', null);
            $searchEndDate   = $request->get('searchEndDate', null);
            $callType        = 'missed';

            $perPage   = $request->get('perPage', 10);
            $sorterKey = $request->get('fieldSorter', null);
            $sorterDirection = $request->get('sorter', null);

            $callsQuery = $this->getCallsViewQuery($search, $searchStartDate, $searchEndDate, $callType, $sorterKey, $sorterDirection);

            $calls = $callsQuery->paginate($perPage ?? 10);


            if ($calls instanceof PaginationClass) {
                $pagination = [
                    'current_page' => $calls->currentPage(),
                    'per_page'     => $calls->perPage(),
                    'total'        => $calls->total(),
                    'last_page'    => $calls->lastPage(),
                ];

                return ['calls' => $calls, 'meta' => $pagination];
            }

            return $calls;
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }

    /**
     * It returns the Asterisk credentials if the password is correct
     *
     * @param Request request The request object
     *
     * @return The current credentials are being returned.
     */
    public function asteriskCredentialsIndex(Request $request)
    {
        $currentCredentials = $this->asteriskCredentials->first();

        if (!$currentCredentials){
            return response()->json(['message' => 'No Credentials Found', 'error' => "Currently there are no credentials!"], 404);
        }

        if($request->internal_pw != $currentCredentials->internal_pw){
            return response()->json(['message' => 'Please insert the correct password', 'error' => "The password inserted is wrong"], 401);
        }

        return $currentCredentials->makeHidden('internal_pw');
    }

    /**
     * It updates the asterisk credentials
     *
     * @param Request request The request object.
     *
     * @return the response of the request.
     */
    public function asteriskCredentialsUpdate(Request $request)
    {
        $currentCredentials = $this->asteriskCredentials->first();
        if ($currentCredentials){
            if ($request->internal_pw != $currentCredentials->internal_pw){
                return response()->json(['message' => 'Please insert the correct password', 'error' => "The password inserted is wrong"], 401);
            }

            $currentCredentials->update($request->all());
        } else {
            $this->asteriskCredentials->create($request->all());
        }

        $newCredentials = $this->asteriskCredentials->first();

        return  response()->json(['Success!' => 'Credentials Updated','New Credentials' => $newCredentials->makeHidden('internal_pw')], 200);
    }
}
