<?php

namespace App\Modules\UniDashboard\Services;

use App\Modules\Business\Enums\KanbanTypesEnum;
use App\Modules\Business\Models\Business;
use App\Modules\Business\Models\BusinessKanban;
use App\Modules\Business\Models\BusinessPayments;
use App\Modules\Business\Models\KanbanHistory;
use App\Modules\UniClients\Models\UniClients;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UniDashboardService
{
    protected $business;
    protected $businessKanban;
    protected $clients;

    public function __construct()
    {
        $this->business = new Business();
        $this->businessKanban = new BusinessKanban();
        $this->clients = new UniClients();
    }


    public function getLeads($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null): int
    {

        return 0;
    }

    public function getNewBusiness($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null): int
    {

        $query = $this->business
            ->where('state_business', '!=', 'fechado')
            ->whereNull('deleted_at');

        if ($this->getUserRole($user) !== 'admin') {
            $query->where(function ($query) use ($user) {
                $query->where('referrer_id', $user)
                    ->orWhere('coach_id', $user)
                    ->orWhere('closer_id', $user);
            });
        } else {
            if (!empty($referrer)) {
                $query->where('referrer_id', $referrer);
            }

            if (!empty($businessCoach)) {
                $query->where('coach_id', $businessCoach);
            }

            if (!empty($closer)) {
                $query->where('closer_id', $closer);
            }
        }

        if (!empty($type)) {
            $query->where('business_kanban_id', $type);
        }

        if (!empty($client)) {
            $query->where('client_id', $client);
        }

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        $result = $query->get();
        return $result->count();
    }

    public function getClosedBusiness($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null): int
    {

        $query = $this->business
            ->where('state_business', 'fechado')
            ->whereNull('deleted_at');

        if ($this->getUserRole($user) !== 'admin') {
            $query->where(function ($query) use ($user) {
                $query->where('referrer_id', $user)
                    ->orWhere('coach_id', $user)
                    ->orWhere('closer_id', $user);
            });
        } else {
            if (!empty($referrer)) {
                $query->where('referrer_id', $referrer);
            }

            if (!empty($businessCoach)) {
                $query->where('coach_id', $businessCoach);
            }

            if (!empty($closer)) {
                $query->where('closer_id', $closer);
            }
        }

        if (!empty($type)) {
            $query->where('business_kanban_id', $type);
        }

        if (!empty($client)) {
            $query->where('client_id', $client);
        }

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        $result = $query->get();
        return $result->count();
    }

    public function getWonBusiness($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null): int
    {

        $query = $this->business
            ->where('closed_state', 'ganho')
            ->whereNull('deleted_at');

        if ($this->getUserRole($user) !== 'admin') {
            $query->where(function ($query) use ($user) {
                $query->where('referrer_id', $user)
                    ->orWhere('coach_id', $user)
                    ->orWhere('closer_id', $user);
            });
        } else {
            if (!empty($referrer)) {
                $query->where('referrer_id', $referrer);
            }

            if (!empty($businessCoach)) {
                $query->where('coach_id', $businessCoach);
            }

            if (!empty($closer)) {
                $query->where('closer_id', $closer);
            }
        }

        if (!empty($type)) {
            $query->where('business_kanban_id', $type);
        }

        if (!empty($client)) {
            $query->where('client_id', $client);
        }

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        $result = $query->get();

        return $result->count();
    }

    public function getLoseBusiness($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null): int
    {
        $query = $this->business
            ->where('closed_state', 'perdido')
            ->whereNull('deleted_at');

        if ($this->getUserRole($user) !== 'admin') {
            $query->where(function ($query) use ($user) {
                $query->where('referrer_id', $user)
                    ->orWhere('coach_id', $user)
                    ->orWhere('closer_id', $user);
            });
        } else {
            if (!empty($referrer)) {
                $query->where('referrer_id', $referrer);
            }

            if (!empty($businessCoach)) {
                $query->where('coach_id', $businessCoach);
            }

            if (!empty($closer)) {
                $query->where('closer_id', $closer);
            }
        }

        if (!empty($type)) {
            $query->where('business_kanban_id', $type);
        }

        if (!empty($client)) {
            $query->where('client_id', $client);
        }

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        $result = $query->get();

        return $result->count();
    }

    public function getNewClients($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null): int
    {

        $query = $this->clients
            ->whereNull('deleted_at');

        if ($this->getUserRole($user) !== 'admin') {
            $query->where('referencer', $user);
        };

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        $result = $query->get();

        return $result->count();
    }

    public function findItem($array, $name, $month)
    {
        foreach ($array as $key => $element) {
            if ($element['name'] === $name && $element['month'] == $month) {
                return $key;
            }
        }
        return false;
    }

    public function getToReceiveBusiness($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null)
    {

        $query = BusinessPayments
            ::whereHas('business', function ($query) use ($user, $referrer, $businessCoach, $closer, $client) {
                if ($this->getUserRole($user) !== 'admin') {
                    $query->where(function ($q) use ($user) {
                        $q->orWhere('referrer_id', $user)
                            ->orWhere('coach_id', $user)
                            ->orWhere('closer_id', $user);
                    });
                } else {
                    if (!empty($referrer)) {
                        $query->where('referrer_id', $referrer);
                    }

                    if (!empty($businessCoach)) {
                        $query->where('coach_id', $businessCoach);
                    }

                    if (!empty($closer)) {
                        $query->where('closer_id', $closer);
                    }
                }

                if (!empty($client)) {
                    $query->where('client_id', $client);
                }

                $query->whereNull('deleted_at');
            })
            ->with('business');

        $result = $query->get();

        Carbon::setLocale('pt_BR');
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->translatedFormat('F');

        $return = [];

        array_push($return, [
            'name' => KanbanTypesEnum::getLabel('COACHING_BUSINESS_CLUB'),
            'month' => $currentMonth,
            'date' => $currentDate,
            'values' => 0
        ]);

        array_push($return, [
            'name' => KanbanTypesEnum::getLabel('DIAGNOSIS'),
            'month' => $currentMonth,
            'date' => $currentDate,
            'values' => 0
        ]);

        $businessKanbanType = $this->businessKanban->get();

        foreach ($result as $key => $value) {
            $typeKanban = KanbanTypesEnum::getLabel($businessKanbanType->first(function ($kanban) use ($value) {
                return $kanban['id'] ===  $value->business->business_kanban_id ? true : false;
            })->type);

            if (!$value->paid_at) {
                $key = $this->findItem($return, $typeKanban, $currentMonth);

                if ($this->getUserRole($user) == 'admin') {
                    $return[$key]['values'] += $value->value;
                } else {
                    $commission = 0;
                    if ($user == $value->business->closer_id) {
                        $commission = $value->business->closer_commission;
                    }
                    if ($user == $value->business->coach_id) {
                        $commission = $value->business->coach_commission;
                    }
                    if ($user == $value->business->referrer_id) {
                        $commission = $value->business->referrer_commission;
                    }

                    $return[$key]['values'] += $commission;
                }
            } else {
                $paidDate = Carbon::createFromFormat('Y-m-d H:i:s', $value->paid_at);
                $paidMonth = $paidDate->translatedFormat('F');

                $key = $this->findItem($return, $typeKanban, $paidMonth);

                $commission = 0;

                if ($this->getUserRole($user) == 'admin') {
                    $commission = $value->value;
                } else {
                    if ($user == $value->business->closer_id) {
                        $commission = $value->business->closer_commission;
                    }
                    if ($user == $value->business->coach_id) {
                        $commission = $value->business->coach_commission;
                    }
                    if ($user == $value->business->referrer_id) {
                        $commission = $value->business->referrer_commission;
                    }
                }

                if ($key !== false) {
                    $return[$key]['values'] += $commission;
                } else {
                    array_push($return, [
                        'name' => $typeKanban,
                        'month' => $paidMonth,
                        'date' => $paidDate,
                        'values' => $commission
                    ]);
                }
            }
        }

        usort($return, function ($a, $b) {
            return $a['date']->timestamp - $b['date']->timestamp;
        });

        return $this->organizeArray($return);
    }

    public function getNewBusinessByType($type = null, $client = null, $startDate = null, $endDate = null, $referrer = null, $businessCoach = null, $closer = null, $user = null): array
    {
        $query = Business::select(
            'business_kanban_id',
            DB::raw("DATE_FORMAT(created_at, '%b') as month"),
            DB::raw("COUNT(id) as total_values"),
            'created_at'
        )
            ->where('closed_state', '!=', 'perdido')
            ->whereNull('canceled_at')
            ->whereNull('deleted_at')
            ->groupBy('business_kanban_id', 'month', DB::raw('YEAR(created_at)'), 'created_at');

        if ($this->getUserRole($user) !== 'admin') {
            $query->where(function ($query) use ($user) {
                $query->where('referrer_id', $user)
                    ->orWhere('coach_id', $user)
                    ->orWhere('closer_id', $user);
            });
        } else {
            if (!empty($referrer)) {
                $query->where('referrer_id', $referrer);
            }

            if (!empty($businessCoach)) {
                $query->where('coach_id', $businessCoach);
            }

            if (!empty($closer)) {
                $query->where('closer_id', $closer);
            }
        }

        if (!empty($type)) {
            $query->where('business_kanban_id', $type);
        }

        if (!empty($client)) {
            $query->where('client_id', $client);
        }

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        $query->orderBy('created_at');

        $result = $query->get();
        $businessKanbanType = $this->businessKanban->get();

        $return = $result->map(function ($item) use ($businessKanbanType) {
            $dateFormat = Carbon::createFromFormat('Y-m-d H:i:s', $item['created_at']);
            $year = $dateFormat->format('Y');
            return [
                'name' => KanbanTypesEnum::getLabel($businessKanbanType->first(function ($kanban) use ($item) {
                    return $kanban['id'] ===  $item['business_kanban_id'] ? true : false;
                })->type),
                'month' => $this->monthPortugues($item['month']) . ' - ' . $year,
                'values' => $item['total_values']
            ];
        })->toArray();


        $combinedData = [];

        foreach ($return as $item) {
            $key = $item['name'] . '_' . $item['month'];

            if (!isset($combinedData[$key])) {
                $combinedData[$key] = $item;
            } else {
                $combinedData[$key]['values'] += $item['values'];
            }
        }

        $combinedData = array_values($combinedData);

        return $this->organizeArray($combinedData);
    }

    public function getHistoryByKanban($kanbanId, $startDate = null, $endDate = null, $user = null)
    {
        $query = KanbanHistory::select(
            'kanban_id',
            'kanban_column_id',
            DB::raw("DATE_FORMAT(created_at, '%b') as month"),
            DB::raw("COUNT(id) as total_values")
        )
            ->with(['kanbanColumn' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['business' => function ($query) {
                $query->withTrashed();
            }])
            ->groupBy('kanban_id', 'kanban_column_id', 'month', DB::raw('YEAR(created_at)'));

        $query->where('kanban_id', $kanbanId);

        if (!empty($startDate) && Carbon::createFromFormat('d/m/Y', $startDate)) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $startDate));
        }

        if (!empty($endDate) && Carbon::createFromFormat('d/m/Y', $endDate)) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $endDate));
        }

        if ($user && $this->getUserRole($user) !== 'admin') {
            $query->whereHas('business', function ($query) use ($user) {
                $query->where('referrer_id', $user)
                    ->orWhere('coach_id', $user)
                    ->orWhere('closer_id', $user);
            });
        }

        $query->orderBy('created_at');

        $result = $query->get();

        $return = $result->map(function ($item) {
            return [
                'name' => $item->kanbanColumn->name,
                'month' => $this->monthPortugues($item['month']),
                'values' => $item['total_values']
            ];
        })->toArray();

        return $return;
    }

    private function getUserRole(int $id)
    {
        return User::where('id', $id)->with('profile')->first()->profile->role;
    }

    private function organizeArray($array)
    {
        $organizedArray = [];

        foreach ($array as $item) {
            $key = $item['month'];
            if (!isset($organizedArray[$key])) {
                $organizedArray[$key] = [];
            }

            if (!isset($organizedArray[$key][$item['name']])) {
                $organizedArray[$key][$item['name']] = 0.0;
            }

            $organizedArray[$key][$item['name']] += (float) $item['values'];
        }

        foreach ($organizedArray as &$monthItems) {
            foreach ($array as $inputItem) {
                if (!isset($monthItems[$inputItem['name']])) {
                    $monthItems[$inputItem['name']] = 0.0;
                }
            }
        }

        $resultArray = [];

        foreach ($organizedArray as $month => $items) {

            uksort($items, function ($a, $b) {
                if ($a === 'clube de empresários') {
                    return -1;
                } elseif ($b === 'clube de empresários') {
                    return 1;
                } else {
                    return strcmp($a, $b);
                }
            });

            foreach ($items as $name => $value) {
                $resultArray[] = [
                    'name' => $name,
                    'month' => $month,
                    'values' => $value
                ];
            }
        }

        return $resultArray;
    }

    private function monthPortugues($month)
    {
        $months = [
            'Jan' => 'Jan',
            'Feb' => 'Fev',
            'Mar' => 'Mar',
            'Apr' => 'Abr',
            'May' => 'Mai',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Ago',
            'Sep' => 'Set',
            'Oct' => 'Out',
            'Nov' => 'Nov',
            'Dec' => 'Dez'
        ];

        // Retorna o mês em português se estiver presente no array, caso contrário, retorna o próprio mês
        return isset($months[$month]) ? $months[$month] : $month;
    }
}
