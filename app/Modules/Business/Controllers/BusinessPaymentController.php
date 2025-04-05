<?php

namespace App\Modules\Business\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Business\Models\BusinessPayments;
use App\Modules\Business\Resources\BusinessPaymentCollection;
use App\Modules\Business\Resources\BusinessPaymentResource;
use App\Modules\Business\Resources\BusinessPaymentResponsibleCollection;
use App\Modules\Business\Resources\BusinessPaymentResponsibleResource;
use App\Modules\Business\Services\BusinessPaymentService;
use App\Modules\Business\Services\BusinessService;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;

class BusinessPaymentController extends Controller
{
    protected $businessService;
    protected $businessPaymentService;

    public function __construct()
    {
        $this->businessService = new BusinessService();
        $this->businessPaymentService = new BusinessPaymentService();
    }

    public function paymentByUser(Request $request, $userId)
    {
        return response()->json([
            "payment" => $this->businessService->getPaymentByUser($userId)
        ]);
    }

    public function show(Request $request)
    {
        try {
            $search  = $request->get('search', null);
            $perPage = $request->get('per_page', 10);
            $sort  = $request->get('sort', null);
            $order  = $request->get('order', null);

            $payments = $this->businessService->getListPayments($search, $perPage, $sort, $order);

            return response()->json([
                'data' => $payments,
                'period' => ucfirst(now()->translatedFormat('F Y')),
                'message' => 'Successfully get current payments',
            ])->header('Access-Control-Allow-Origin', '*', true);
        } catch (\Exception $th) {
            return response()->json([
                'message' => 'Failed to retrieve payments',
                'error' => $th->getMessage(),
            ], 500)->header('Access-Control-Allow-Origin', '*', true);
        }
    }

    public function details(Request $request)
    {
        try {
            $date = null;
            if ($request->date) {
                $date = $request->date;
            }

            $payments = $this->businessService->getListPaymentsDetails($request->user_id, $date);

            return response()->json([
                'data' => $payments,
                'period' => ucfirst(now()->translatedFormat('F Y')),
                'message' => 'Successfully get current payments',
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'message' => 'Failed to retrieve payments',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function listPaymentsHistoric(Request $request)
    {
        try {
            $search  = $request->get('search', null);
            $perPage = $request->get('per_page', 10);
            $sort  = $request->get('sort', null);
            $order  = $request->get('order', null);

            $payments = $this->businessService->getListPaymentsHistoric($search, $perPage, $order);

            return response()->json([
                'data' => $payments,
                'message' => 'Successfully get payments historic',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve payments historic',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function listGroupPaymentHistoric(Request $request)
    {
        try {
            $user_id = $request->get('user_id', null);
            $period  = $request->get('period', null);
            $start   = $request->get('startDate', null);
            $end     = $request->get('endDate', null);

            $payments = $this->businessService->getListGroupPaymentHistoric($user_id, $period, $start, $end);
            $user = User::find($request->user_id);

            return response()->json([
                'data' => $payments,
                'user_name' => $user->name,
                'message' => 'Successfully get payments historic',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve payments historic',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    //===========================================

    public function listPayments(Request $request)
    {
        try {
            $user = $request->user();
            $isAdmin = $user->profile->role === 'admin';
            $userId = $isAdmin ? null : $user->id;

            $pendent   = $request->get('pendents') == "true" ? true : false;
            $search   = $request->get('search', null);
            $perPage   = $request->get('per_page', 15);
            $start   = $request->get('startDate', null);
            $end     = $request->get('endDate', null);

            $start = $start ? Carbon::createFromFormat('d/m/Y', $start)->startOfDay() : null;
            $end = $end ? Carbon::createFromFormat('d/m/Y', $end)->endOfDay() : null;

            $list = $this->businessPaymentService->listBusiness($pendent, $start, $end, $perPage, $search, $userId);

            return (new BusinessPaymentCollection($list))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listPaymentResponsible(Request $request, BusinessPayments $id)
    {
        try {
            $user = $request->user();
            $isAdmin = $user->profile->role === 'admin';
            $userId = $isAdmin ? null : $user->id;

            $list = $this->businessPaymentService->listBusinessResposible($id, $userId);

            return (new BusinessPaymentResponsibleCollection($list))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payments responsible',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function made(Request $request, BusinessPayments $id)
    {
        try {
            $user = $request->user();
            if ($user->profile->role !== 'admin') {
                return throw new Exception("Usuário sem permissão", 401);
            }

            $businessPayment = $this->businessPaymentService->madePayment($id);

            return (new BusinessPaymentResource($businessPayment))
                ->response()->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to made payment to business',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function generate(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->profile->role !== 'admin') {
                return throw new Exception("Usuário sem permissão", 401);
            }

            $this->businessPaymentService->generatePayments();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to made payment to business',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
