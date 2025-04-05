<?php

namespace App\Modules\Business\Services;

use App\Modules\ActiveCampaign\Services\ActiveCampaignService;
use App\Modules\Business\Enums\CommissionMethodEnum;
use App\Modules\Business\Enums\KanbanTypesEnum;
use App\Modules\Business\Models\Business;
use App\Modules\Business\Models\BusinessPaymentsResponsible;
use App\Modules\Users\Models\User;
use App\Modules\Business\Models\BusinessKanbanColumns;
use App\Modules\Business\Models\KanbanHistory;
use App\Modules\Clients\Models\Clients;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\UniClients\Models\UniClients;
use App\Services\Service;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class BusinessService extends Service
{
    protected $business;
    protected $businessKanbanColumnsModel;
    private $acService;
    private $notificationService;

    public function __construct()
    {
        $this->business = new Business();
        $this->businessKanbanColumnsModel = new BusinessKanbanColumns();
        $this->acService = new ActiveCampaignService();
        $this->notificationService = new NotificationService();
    }

    public function listBusinesses($request, $user = null)
    {
        $isAdmin = $user->profile->role === 'admin';

        $userId = $isAdmin ? null : $user->id;

        $business = $this->business
            ->with('businessKanban')
            ->with('stage')
            ->with(['client' => function ($query) {
                $query->withTrashed();
            }]);

        if (!empty($request['search'])) {
            $business->where('name', 'like', '%' . $request['search'] . '%');
        }

        if ($userId) {
            $business->where('referrer_id', $userId);
        }

        if (!empty($request['state_business'])) {
            $business->where('state_business', $request['state_business']);
        }

        if (!empty($request['typeKanban']) && $request['typeKanban'] !== 'all') {
            $business->where('business_kanban_id', $request['typeKanban']);
        }

        if (!empty($request['start_date']) && !empty($request['end_date'])) {
            $business->whereBetween('created_at', [Carbon::createFromFormat('d/m/Y', $request['start_date'])->startOfDay(), Carbon::createFromFormat('d/m/Y', $request['end_date'])->endOfDay()]);
        }
        if (!empty($request['owner_business'])) {
            $business->where('referrer_id', $request['owner_business']);
        }
        if (!empty($request['client_business'])) {
            $business->where('client_id', $request['client_business']);
        }

        if (!empty($request['sort'])) {
            if ($request['sort'] === 'new') {
                $business->orderBy('closed_at', 'desc');
            }
            if ($request['sort'] === 'old') {
                $business->orderBy('closed_at', 'asc');
            }
        } else {
            $business->orderBy('updated_at', 'asc');
        }

        return $this->result($business->paginate(!empty($request['per_page']) ? $request['per_page'] : 10), true);
    }

    public function create($data)
    {
        try {
            $client = UniClients::where('id', $data['client_id'])->with('referrer')->first();
            $existingBusiness = $this->business
                ->where('client_id', $client->id)
                ->where('business_kanban_id', $data['business_kanban_id'])
                ->where('state_business', 'aberto')
                ->count();

            if ($existingBusiness > 0) {
                throw new \Exception('O cliente já possui um negócio no tipo de funil selecionado em aberto.');
            }

            $business = $this->business->create($data);
            // if () {
            // $data['id'] = $business->id;
            // $this->createPayments($data);
            // }

            $history = new KanbanHistory();
            $history->fill([
                'business_id' => $business->id,
                'kanban_id' => $business->business_kanban_id,
                'kanban_column_id' => $business->stage,
            ]);
            $history->save();

            $acId = $this->acService->createDeal($business, $business->client);

            if ($acId) {
                $business->acId = $acId;
            }

            $business->save();

            if ($client->referrer->id !== auth()->user()->id) {
                $this->notificationService->addNotification(
                    $client->referrer->id,
                    'O utilizador ' . auth()->user()->name . ' criou um negócio para o seu cliente ' . $client->name,
                    'non_referrer_create_business',
                    $business,
                    $business->id
                );
            }
            return $this->result($business, true);
        } catch (\Throwable $th) {
            return $this->result($th->getMessage(), false);
        }
    }

    private function checkPaymentExist($payment, $responsible)
    {
        return BusinessPaymentsResponsible::where("business_id", $payment['id'])
            ->where('responsible', $responsible)
            ->get();
    }

    private function removePayments($BusinessPaymentsResponsible, $paymentType, $userId, $value)
    {
        // $wasDeleted = false;
        // foreach ($BusinessPaymentsResponsible as $payment) {
        //     if (
        //         $payment->payment_type != $paymentType ||
        //         $payment->user_id != $userId ||
        //         $payment->value != $value
        //     ) {
        //         $wasDeleted = true;
        //         $payment->delete();
        //     }
        // }

        // return $wasDeleted;
    }

    // public function createPayments($request)
    // {
    //     if (!empty($request['referrer_commission'])) {
    //         $BusinessPaymentsResponsible = $this->checkPaymentExist($request, 'referrer');
    //         if (!$BusinessPaymentsResponsible->isEmpty()) {
    //             if ($this->removePayments(
    //                 $BusinessPaymentsResponsible,
    //                 $request["referrer_commission_method"],
    //                 $request['referrer_id'],
    //                 $request['referrer_commission']
    //             )) {
    //                 $this->newPayment(
    //                     $request['referrer_commission_method'],
    //                     $request['referrer_id'],
    //                     $request['id'],
    //                     $request['referrer_commission'],
    //                     'referrer'
    //                 );
    //             }
    //         } else {
    //             $this->newPayment(
    //                 $request['referrer_commission_method'],
    //                 $request['referrer_id'],
    //                 $request['id'],
    //                 $request['referrer_commission'],
    //                 'referrer'
    //             );
    //         }
    //     }

    //     if (!empty($request['coach_commission'])) {
    //         $BusinessPaymentsResponsible = $this->checkPaymentExist($request, 'coach');
    //         if (!$BusinessPaymentsResponsible->isEmpty()) {
    //             if ($this->removePayments(
    //                 $BusinessPaymentsResponsible,
    //                 $request["coach_commission_method"],
    //                 $request['coach_id'],
    //                 $request['coach_commission']
    //             )) {
    //                 $this->newPayment(
    //                     $request['coach_commission_method'],
    //                     $request['coach_id'],
    //                     $request['id'],
    //                     $request['coach_commission'],
    //                     'coach'
    //                 );
    //             }
    //         } else {
    //             $this->newPayment(
    //                 $request['coach_commission_method'],
    //                 $request['coach_id'],
    //                 $request['id'],
    //                 $request['coach_commission'],
    //                 'coach'
    //             );
    //         }
    //     }

    //     if (!empty($request['closer_commission'])) {
    //         $BusinessPaymentsResponsible = $this->checkPaymentExist($request, 'closer');
    //         if (!$BusinessPaymentsResponsible->isEmpty()) {
    //             if ($this->removePayments(
    //                 $BusinessPaymentsResponsible,
    //                 $request["closer_commission_method"],
    //                 $request['closer_id'],
    //                 $request['closer_commission']
    //             )) {
    //                 $this->newPayment(
    //                     $request['closer_commission_method'],
    //                     $request['closer_id'],
    //                     $request['id'],
    //                     $request['closer_commission'],
    //                     'closer'
    //                 );
    //             }
    //         } else {
    //             $this->newPayment(
    //                 $request['closer_commission_method'],
    //                 $request['closer_id'],
    //                 $request['id'],
    //                 $request['closer_commission'],
    //                 'closer'
    //             );
    //         }
    //     }
    // }

    // public function newPayment($method, $user, $business, $value, $responsible)
    // {
    // $qtd  = $this->methodQtd($method);
    // for ($i = 0; $i < $qtd; $i++) {
    //     $date  = date("Y-m-d", strtotime("+{$i} month", strtotime(now())));
    //     BusinessPaymentsResponsible::create([
    //         'user_id' => $user,
    //         'business_id' =>  $business,
    //         'payment_type' => $method,
    //         'responsible' =>  $responsible,
    //         'value' => $value / $qtd,
    //         'date' => $date,
    //         'sequence' =>  $i + 1,
    //         'fl_recurrent' => $this->flRecurrent($method),
    //         'fl_closed' => $this->flClosed($method),
    //     ]);
    // }
    // }

    public function methodQtd($method)
    {
        switch ($method) {
            case '3x':
                return 3;
                break;

            case '6x':
                return 6;
                break;

            case '12x':
                return 12;
                break;

            default:
                return 1;
                break;
        }
    }

    public function flRecurrent($method)
    {
        return $method == 'recorrente' ? true : false;
    }

    public function flClosed($method)
    {
        return $method == 'encerrar pagamento' ? true : false;
    }

    public function findById(int $id)
    {
        try {
            $business = $this->business
                ->with(['client' => function ($query) {
                    $query->withTrashed();
                }])
                ->with('referrer')
                ->with('coach')
                ->with('closer')
                ->with('businessKanban')
                ->with('product')
                ->find($id);

            return $this->result($business, true);
        } catch (\Throwable $th) {
            return $this->result($th->getMessage(), false);
        }
    }

    public function cancelOneBusiness($id)
    {
        $business = $this->business->find($id);

        $businessKanbanColumn = $this->businessKanbanColumnsModel->where([
            'business_kanban_id' => $business->business_kanban_id,
            'is_last' => true
        ])->first();

        $history = new KanbanHistory();
        $history->fill([
            'business_id' => $business->id,
            'kanban_id' => $business->business_kanban_id,
            'kanban_column_id' => $businessKanbanColumn->id,
        ]);
        $history->save();

        $business->state_business = 'fechado';
        $business->closed_state = 'perdido';
        $business->stage = $businessKanbanColumn->id;
        $business->canceled_at = Carbon::now();
        $business->closed_at = Carbon::now();
        $business->save();

        if ($business->acId) {
            $this->acService->moveStage($business);
        }

        // $this->updatePayment($id, 'canceled');
    }

    public function closeOneBusiness($id)
    {
        $business = $this->business->find($id);

        $businessKanbanColumn = $this->businessKanbanColumnsModel->where([
            'business_kanban_id' => $business->business_kanban_id,
            'is_last' => true
        ])->first();

        $history = new KanbanHistory();
        $history->fill([
            'business_id' => $business->id,
            'kanban_id' => $business->business_kanban_id,
            'kanban_column_id' => $businessKanbanColumn->id,
        ]);
        $history->save();

        $business->state_business = 'fechado';
        // $business->referrer_commission_method = CommissionMethodEnum::CLOSEPAYMENT;
        // $business->coach_commission_method = CommissionMethodEnum::CLOSEPAYMENT;
        // $business->closer_commission_method = CommissionMethodEnum::CLOSEPAYMENT;
        $business->stage = $businessKanbanColumn->id;
        $business->closed_at = Carbon::now();
        $business->save();

        if ($business->acId) {
            $this->acService->moveStage($business);
        }
    }

    public function reopenOneBusiness($id)
    {
        $business = $this->business->find($id);
        $businessKanbanColumn = $this->businessKanbanColumnsModel->where([
            'business_kanban_id' => $business->business_kanban_id,
            'is_first' => true
        ])->first();

        $business->state_business = 'aberto';
        $business->stage = $businessKanbanColumn->id;
        $business->closed_state = 'aberto';
        $business->canceled_at = null;
        $business->closed_at = null;
        $business->save();

        if ($business->acId) {
            $this->acService->moveStage($business);
        }

        // $this->updatePayment($id, 'pending');
    }

    public function updateState($id, $data)
    {
        $business = $this->business->find($id);
        $business->closed_state = $data['state_business'];

        // $this->getStatusAndUpdatePayment($id, $business->closed_state);

        $business->save();

        if ($business->acId) {
            $this->acService->moveStage($business);
        }
    }

    // public function getStatusAndUpdatePayment($id, $closedState)
    // {
    //     $status = 'pending';
    //     if ($closedState === 'aberto') {
    //         $status = 'pending';
    //     } else if ($closedState === 'ganho') {
    //         $status = 'approved';
    //     } else if ($closedState === 'perdido') {
    //         $status = 'canceled';
    //     }

    //     return $this->updatePayment($id, $status);
    // }

    // public function updatePayment($id, $status)
    // {
    // $payments = new BusinessPaymentsResponsible();
    // $paymentList = $payments->where('business_id', $id)->get();

    // $paymentList->each(function ($payment) use ($status) {
    //     $payment->status = $status;
    //     $payment->save();
    // });
    // }

    public function updateOneBusiness($id, $data)
    {
        $business = $this->business
            ->with('businessKanban')
            ->find($id);

        if (!empty($data['state_business']) && $data['state_business'] == 'fechado') {
            // $this->getStatusAndUpdatePayment($id, $data['closed_state']);

            $data['closed_at'] = now();
        } else {
            $data['closed_at'] = null;

            // $this->updatePayment($id, 'pending');
        }

        // $this->createPayments(array_merge($data, ['id' => $id]));

        if (!empty($data['stage']) && $business->stage != $data['stage']) {
            $history = new KanbanHistory();
            $history->fill([
                'business_id' => $business->id,
                'kanban_id' => $business->business_kanban_id,
                'kanban_column_id' => $data['stage'],
            ]);
            $history->save();
        }

        if (!empty($data['client_id'])) {
            $clientIsAvailable = $this->business->where(function ($query) use ($business, $data) {
                $query->where('business_kanban_id', $business->business_kanban_id)
                    ->where('client_id', $data['client_id'])
                    ->where('state_business', 'aberto');
            })->exists();

            if (!$clientIsAvailable) {
                $business->update($data);

                if ($business->acId) {
                    $this->acService->updateDeal($business);
                }

                return $business;
            }
        } else {
            $business->update($data);

            if ($business->acId) {
                $this->acService->updateDeal($business);
            }

            return $business;
        }

        throw new \Exception('Este cliente já possui um negócio do tipo ' . KanbanTypesEnum::getLabel($business->businessKanban->type) . ' em aberto.');
    }

    public function getListPayments($search, $perPage, $sort, $order)
    {
        try {
            $result = BusinessPaymentsResponsible::selectRaw('
                    users.id as user_id,
                    users.name,
                    COUNT(user_id) as business_count,
                    SUM(business_payments.value) as commissions
                ')
                ->join('users', 'user_id', 'users.id')
                ->join('business', 'business_id', 'business.id')
                ->where('status', '!=', 'canceled')
                ->where('business.closed_state', '!=', 'perdido')
                ->where(function ($q) {
                    $q->where(function ($sq) {
                        $sq->where('fl_recurrent', '!=', 1)
                            ->whereMonth('date', date('m', strtotime(now())))
                            ->whereYear('date', date('Y', strtotime(now())));
                    })
                        ->orWhere(function ($q) {
                            $q->where('fl_recurrent', '=', 1)
                                ->where('status', '!=', 'canceled');
                        });
                })
                ->when($search, function ($query, $search) {
                    return $query->where('users.name', 'like', '%' . $search . '%');
                })
                ->when($order, fn ($query) => $query->orderBy('users.name', $order))
                ->groupBy('users.name')
                ->groupBy('users.id');

            return $perPage ? $result->paginate($perPage ?? 10) : $result->get();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage(), $th->getCode());
        }
    }

    public function getListPaymentsDetails($user_id, $date = null)
    {
        try {
            $datePeriod = now();
            $history = false;
            if ($date) {
                $history = true;
                $datePeriod = Carbon::createFromFormat('Y-m-d H:i:s', $date);
            }

            $result = BusinessPaymentsResponsible::selectRaw('
                    users.id as user_id,
                    business.name,
                    responsible as function,
                    business_payments.value as value,
                    business.closed_state
                ')
                ->join('users', 'user_id', 'users.id')
                ->join('business', 'business_id', 'business.id')
                ->where('user_id', $user_id);


            if ($history) {
                $result->where('status', 'approved')
                    ->where('business.closed_state', 'ganho');
            } else {
                $result->where('status', '!=', 'canceled')
                    ->where('business.closed_state', '!=', 'perdido');
            }

            $result->where(function ($q) use ($datePeriod) {
                $q->where(function ($sq) use ($datePeriod) {
                    $sq->where('fl_recurrent', '!=', 1)
                        ->whereMonth('date', date('m', strtotime($datePeriod)))
                        ->whereYear('date', date('Y', strtotime($datePeriod)));
                })
                    ->orWhere(function ($sq) use ($datePeriod) {
                        $sq->where('fl_recurrent', '=', 1)
                            ->whereBetween('date', [
                                $datePeriod->startOfMonth()->format('Y-m-01'),
                                $datePeriod->endOfMonth()->format('Y-m-t')
                            ]);
                    });
            });

            return $result->get();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage(), $th->getCode());
        }
    }

    public function getPaymentByUser($userId)
    {
        return BusinessPaymentsResponsible::where('user_id', $userId)->with('business')->with('user')->get();
    }

    public function getListPaymentsHistoric($search, $perPage, $sorter)
    {
        try {
            $result = User::select('users.name', 'users.id')
                ->join('business_payments', 'business_payments.user_id', 'users.id')
                ->where('date', '<=', now()->subMonths(1))
                ->when($search, function ($query, $search) {
                    return $query->where('users.name', 'like', '%' . $search . '%');
                })
                ->when($sorter, fn ($query) => $query->orderBy('users.name', $sorter))
                ->groupBy('users.name')
                ->groupBy('users.id');

            return $perPage ? $result->paginate($perPage ?? 10) : $result->get();
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), $th->getCode());
        }
    }

    public function getListGroupPaymentHistoric($user_id, $period, $start, $end)
    {
        try {
            $payments = BusinessPaymentsResponsible::selectRaw('business_payments.*, users.name as user_name, business.*')
                ->join('users', 'user_id', 'users.id')
                ->join('business', 'business_id', 'business.id')
                ->where('user_id', $user_id)
                ->where(function ($q) use ($period, $start, $end) {
                    $q->where('fl_recurrent', '!=', 1)
                        ->when(!$period, function ($q) {
                            return $q->where('date', '<=', now()->subMonths(1));
                        })
                        ->when($period, function ($q) use ($period) {
                            return $q->whereBetween('date', [now()->subMonths($period), now()]);
                        })
                        ->when($start, function ($q) use ($start, $end) {
                            return $q->whereBetween('date', [$start, $end]);
                        });
                })
                ->orWhere(function ($q) use ($user_id, $period, $start, $end) {
                    $q->where('fl_recurrent', '=', 1)
                        ->where('closed_state', '=', 'ganho')
                        ->where('user_id', $user_id)
                        ->when(!$period, function ($q) {
                            return $q->where('date', '<=', now()->subMonths(1));
                        })
                        ->when($period, function ($q) use ($period) {
                            return $q->whereBetween('date', [now()->subMonths($period), now()]);
                        })
                        ->when($start, function ($q) use ($start, $end) {
                            return $q->whereBetween('date', [$start, $end]);
                        });
                })
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->date)->format('Y');
                });

            $result = $payments->map(function ($values) {
                return $values->groupBy(function ($val) {
                    return Carbon::parse($val->date)->format('n');
                });
            })->toArray();

            return $result;
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), $th->getCode());
        }
    }

    public function changeResponsiblesToAdmin($currentUser, $adminUser)
    {
        $this->business->where('referrer_id', $currentUser->id)->update(['referrer_id' => $adminUser->id]);
        $this->business->where('coach_id', $currentUser->id)->update(['coach_id' => $adminUser->id]);
        $this->business->where('closer_id', $currentUser->id)->update(['closer_id' => $adminUser->id]);
    }
}
