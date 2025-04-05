<?php

namespace App\Modules\Calls\Controllers;

use App\Modules\Calls\Requests\AddOrRemoveCallsOnCacheRequest;
use App\Modules\Calls\Requests\UpdateCallRequest;
use App\Modules\Calls\Services\AsteriskService;
use App\Modules\Calls\Services\CallsService;
use App\Modules\Clients\Models\Clients;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CallsController extends Controller
{
    private $asteriskService;
    private $callsService;
    private $ordersServices;

    public function __construct()
    {
        $this->asteriskService = new AsteriskService();
        $this->callsService     = new CallsService();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inProgress(Request $request)
    {
        try{
            $calls = $this->asteriskService->getCallsInProgress();
            
            return response()->json([
                'calls' => $calls
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOne($callId)
    {
        try{
            $calls = $this->asteriskService->getOneCall($callId);

            return response()->json($calls);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hangup(Request $request)
    {
        try{
            $calls = $this->asteriskService->getCallsHangup($request);

            return response()->json($calls);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missed(Request $request)
    {
        try {
            $calls = $this->asteriskService->getCallsMissed($request);

            return response()->json($calls);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }


    public function getOrdersDetailsByClientId(Clients $client) {
        try{
            $products = $this->ordersServices->getProductsOrdersByClientId($client);
            $orders = $this->ordersServices->getClientFilteredOrders($client);
            return response()->json([
                'products' => $products,
                'orders' => $orders
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function closeGhostCall($callId){
        try{
            $this->callsService->terminateCall($callId);
            return response()->json(['Call with ID: '.$callId.' has been terminated successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateCall(UpdateCallRequest $updateCallRequest){
        try{
            $this->callsService->updateCall($updateCallRequest);
            return response()->json(['Call updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getAllCallsBlocked()
    {
        try {
            $blockedCalls = $this->callsService->getAllCallsBlocked();
            return response()->json([
                'calls' => $blockedCalls
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function setCallBlockedOnCache(AddOrRemoveCallsOnCacheRequest $addCallToCacheRequest)
    {
        try {
            $blockedCalls = $this->callsService->setCallsBlockedOnCache(
                Auth::user()->id,
                $addCallToCacheRequest->call_id,
            );

            return response()->json([
                'calls' => $blockedCalls
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function removeCallBlockedFromCache(AddOrRemoveCallsOnCacheRequest $removeCallFromCacheRequest)
    {
        try {
            $blockedCalls = $this->callsService->removeCallsBlockedFromCache(
                Auth::user()->id,
                $removeCallFromCacheRequest->call_id
            );

            return response()->json([
                'calls' => $blockedCalls
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function removeAllCallsBlockedFromCache()
    {
        try {
            $this->callsService->removeAllCallsBlockedFromCache();

            return response()->json(['All Calls Removed with Success!']);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    public function exportCallsHangup(Request $request){
        try {
            $data = $this->asteriskService->exportCallsHangup($request);
            $html = view('calls.answeredCalls', $data)->render();

            $pdf = new Dompdf();
            $pdf->setPaper('A4');
            $pdf->loadHtml($html);
            $pdf->render();

            return $pdf->stream('Chamadas Atentidas.pdf');
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }


    //TODO Only for testing
    //TODO Remove Later
    public function clearPhones() {
        try {
            $this->callsService->scramblePhones();

            return response()->json(['All phones scrambled with Success!']);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'error',
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }
}
