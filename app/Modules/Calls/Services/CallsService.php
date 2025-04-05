<?php

namespace App\Modules\Calls\Services;

use App\Modules\Calls\Models\AsteriskCall;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

class CallsService
{
    private $asteriskCall;

    const CACHE_BLOCKED_CALLS = 'blockedCalls';

    public function __construct()
    {
        $this->asteriskCall = new AsteriskCall();
    }

    /**
     * It saves the event to the database
     *
     * @param EventMessage event The name of the event.
     */
    public function terminateCall($callId)
    {
        $asteriskCall = $this->asteriskCall->where('id', $callId)->first();

        if(!$asteriskCall){
            throw new Exception('No call was found with the given id!', 404);
        }

        if ($asteriskCall && $asteriskCall->status === 'connected') {
            $asteriskCall->update([
                'status' => 'hangup',
                'hangup_status' => 16
            ]);
            return;
        }
        throw new Exception('Incorrectly trying to terminate a Call !', 400);
    }

    public function updateCall($request){
        if (isset($request['id'])) {
            $call = $this->asteriskCall->find($request['id']);
            
            $updateData = [];
            
            if (isset($request['call_operator'])) {
                $updateData['call_operator'] = $request['call_operator'];
            }

            if (isset($request['call_reason'])) {
                $updateData['call_reason'] = $request['call_reason'];
            }

            if (isset($request['status'])) {
                $updateData['status'] = $request['status'];
            }
            
            if (isset($request['hangup_status'])) {
                $updateData['hangup_status'] = $request['hangup_status'];
            }

            if (empty($updateData)) {
                throw new exception('No data was given!', 422);
            }

            $call->update($updateData);
        }
    }

    public function getAllCallsBlocked()
    {
        $blockedCalls = Cache::get(self::CACHE_BLOCKED_CALLS, []);

        return $blockedCalls;
    }

    private function getCallsFromCacheByCallId($cachedBlockedCalls, $order_id)
    {
        return array_search($order_id, array_column($cachedBlockedCalls, 'call_id')); // Returns the key of the searched element or false if does not find it
    }

    public function setCallsBlockedOnCache($user_id, $call_id)
    {
        $cachedBlockedCalls = Cache::get(self::CACHE_BLOCKED_CALLS, []);
        
        $keyCall = $this->getCallsFromCacheByCallId($cachedBlockedCalls, $call_id);
        
        if ( $keyCall !== false && $user_id != $cachedBlockedCalls[$keyCall]['user_id'] ) {
            throw new \Exception('The given call is already blocked by another user!', 400);
        } else {
            $userHasCallIdx = array_search($user_id, array_column($cachedBlockedCalls, 'user_id')); // Returns the key of the searched element or false if does not find it
            
            if ($userHasCallIdx !== false && $userHasCallIdx  >= 0 ) {
                unset($cachedBlockedCalls[$userHasCallIdx]);
            }
            $newCall = compact('user_id', 'call_id');
            $cachedBlockedCalls = array_merge($cachedBlockedCalls, [$newCall]);

            Cache::put(self::CACHE_BLOCKED_CALLS, $cachedBlockedCalls);
        }
    
        return $cachedBlockedCalls;
    }

    public function removeCallsBlockedFromCache($user_id, $call_id)
    {
        $cachedBlockedCalls = Cache::get(self::CACHE_BLOCKED_CALLS, []);
        $keyCall = array_search($call_id, array_column($cachedBlockedCalls, 'call_id'));

        if ($keyCall !== false) {
            if($cachedBlockedCalls[$keyCall]['user_id'] == $user_id){
                unset($cachedBlockedCalls[$keyCall]);
                Cache::put(self::CACHE_BLOCKED_CALLS, array_values($cachedBlockedCalls));
            }else{
                throw new \Exception('No Orders found in cache for authenticated user', 400);
            }
            return $cachedBlockedCalls;
        } else {
            throw new \Exception('The given order in not in cache!', 400);
        }
    }

    public function removeAllCallsBlockedFromCache()
    {
        $cachedBlockedCalls = [];
        Cache::put(self::CACHE_BLOCKED_CALLS, $cachedBlockedCalls);
    }

    //TODO Only for testing
    //TODO Remove Later
    public function scramblePhones() {
        $calls = $this->asteriskCall
            ->where('status', 'connected')
            ->orWhereIn('hangup_status', ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603'])
            ->get();

        $calls->each(function ($call) {
            $randomPhoneNumber = str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        
            // Update the caller_phone field with the random number
            $call->update(['caller_phone' => $randomPhoneNumber]);
        });
    }
}
