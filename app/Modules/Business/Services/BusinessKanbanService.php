<?php

namespace App\Modules\Business\Services;

use App\Modules\ActiveCampaign\Services\ActiveCampaignService;
use App\Modules\Business\Enums\KanbanTypesEnum;
use App\Modules\Business\Models\Business;
use App\Modules\Business\Models\BusinessKanban;
use App\Modules\Business\Models\BusinessKanbanColumns;
use App\Modules\Business\Models\BusinessPayments;
use App\Modules\Business\Models\KanbanHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class BusinessKanbanService
{
    /**
     * @var BusinessKanban
     */
    protected BusinessKanban $businessKanbanModel;

    /**
     * @var BusinessKanbanColumns
     */

    /**
     * @var Business
     */
    protected Business $businessModel;

    /**
     * @var KanbanHistory
     */
    protected Business $kanbanHistoryModel;

    protected BusinessKanbanColumns $businessKanbanColumnsModel;

    private ActiveCampaignService $acService;

    public function __construct()
    {
        $this->businessKanbanModel = new BusinessKanban();
        $this->businessKanbanColumnsModel = new BusinessKanbanColumns();
        $this->businessModel = new Business();
        $this->acService = new ActiveCampaignService();
    }

    public function listBusinessKanbanAndColumns($type = null, $request = null, $user = null)
    {
        $isAdmin = $user->profile->role === 'admin';

        $userId = $isAdmin ? null : $user->id;

        $request = $request ? $request : [];

        $query = $this->businessKanbanColumnsModel
            ->with(['business' => function ($query) use ($request, $userId) {
                if (!empty($request['search'])) {
                    $search = $request['search'];
                    $query->where('name', 'like', "%$search%");
                }

                if (!empty($request['start_date']) && !empty($request['end_date'])) {
                    $query->whereBetween('created_at', [Carbon::createFromFormat('d/m/Y', $request['start_date'])->startOfDay(), Carbon::createFromFormat('d/m/Y', $request['end_date'])->endOfDay()]);
                }

                if (!empty($request['owner_business'])) {
                    $query->where('referrer_id', $request['owner_business']);
                }

                if ($userId) {
                    $query->where(function ($q) use ($userId) {
                        $q->orWhere('referrer_id', $userId)
                            ->orWhere('coach_id', $userId)
                            ->orWhere('closer_id', $userId);
                    });
                }

                if (!empty($request['client_business'])) {
                    $query->where('client_id', $request['client_business']);
                }

                if (!empty($request['sort'])) {
                    if ($request['sort'] === 'new') {
                        $query->orderBy('created_at', 'desc');
                    }
                    if ($request['sort'] === 'old') {
                        $query->orderBy('created_at', 'asc');
                    }
                } else {
                    $query->orderBy('index', 'asc');
                }
                $query->with(['followUp' => function ($query) {
                    $query->where('completed', false)
                        ->orderByRaw('CONCAT(date, " ", time) asc');
                }]);
                $query->with(['client' => function ($query) {
                    $query->withTrashed();
                }]);
            }])
            ->orderBy('index', 'asc');

        if ($type) {
            $kanbanType = KanbanTypesEnum::getType($type);
            $query->whereHas('businessKanban', function ($query) use ($kanbanType) {
                $query->where('type', $kanbanType);
            });
        }

        $result = $query->get();

        $businessKanban = [];

        foreach ($result as $value) {
            $businessKanbanId = $value->businessKanban->id;

            if (!isset($businessKanban[$businessKanbanId])) {
                $businessKanban[$businessKanbanId] = $value->businessKanban->toArray();
                $businessKanban[$businessKanbanId]['columns'] = [];
            }


            $columnData = $value->toArray();
            unset($columnData['business_kanban']);

            if ($value->is_last == true) {
                // reordena os negocios da ultima coluna de acordo com o status de fechamento e atualiza o index.
                usort($columnData['business'], function ($a, $b) {
                    $order = [
                        'aberto' => 1,
                        'ganho' => 2,
                        'perdido' => 3,
                    ];

                    $closedStateA = isset($order[$a['closed_state']]) ? $order[$a['closed_state']] : PHP_INT_MAX;
                    $closedStateB = isset($order[$b['closed_state']]) ? $order[$b['closed_state']] : PHP_INT_MAX;

                    return $closedStateA - $closedStateB;
                });

                $index = 0;
                foreach ($columnData['business'] as &$item) {
                    $item['index'] = $index++;

                    Business::where('id', $item['id'])->update(['index' => $item['index']]);
                }
            }

            $businessKanban[$businessKanbanId]['columns'][] = $columnData;
        }

        $sortedBusinessKanban = collect($businessKanban)->sortBy('index')->values()->all();

        return $type ? current($sortedBusinessKanban) : $sortedBusinessKanban;
    }

    public function listBusinessKanbans()
    {
        return $this->businessKanbanModel->get();
    }

    public function createBusinessKanban($data)
    {
        $board = $this->businessKanbanModel->where('type', KanbanTypesEnum::getType($data['kanban_type']))->firstOrFail();

        if (!$board) {
            throw new \Exception("O quadro não foi encontrado.");
        }

        $existingColumn = $this->businessKanbanColumnsModel
            ->where('business_kanban_id', $board->id)
            ->where(function ($query) use ($data) {
                $query->where('name', $data['title'])
                    ->orWhere('color', $data['color']);
            })
            ->first();

        if ($existingColumn) {
            throw new \Exception("Uma coluna com o nome ou cor já existe.");
        }

        $lastColumn = $this->businessKanbanColumnsModel->where('business_kanban_id', $board->id)->orderBy('index', 'desc')->first();

        $businessKanbanColumn = $this->businessKanbanColumnsModel->create([
            'name' => $data['title'],
            'color' => $data['color'],
            'index' => $lastColumn->index,
            'business_kanban_id' => $board->id,
        ]);

        $lastColumn->index = $lastColumn->index + 1;
        $lastColumn->save();

        return $businessKanbanColumn;
    }

    public function updateBusinessKanbanColumn($id, $data)
    {
        $column = $this->businessKanbanColumnsModel->findOrFail($id);

        $column->update([
            'name' => $data['title'],
            'color' => $data['color'],
        ]);

        return $column;
    }

    public function deleteBusinessKanbanColumn($id)
    {
        $column = $this->businessKanbanColumnsModel->findOrFail($id);
        $column->delete();

        return true;
    }

    public function moveColumnInBusinessKanban($data)
    {
        foreach ($data['columns'] as $column) {
            $this->businessKanbanColumnsModel
                ->where('id', $column['id'])
                ->where('business_kanban_id', $data['business_kanban_id'])
                ->update([
                    'index' => $column['index'],
                ]);
        };
    }

    public function moveCardInBusinessKanban($data)
    {
        $column = $this->businessKanbanColumnsModel->where(['id' => $data['columnKey']])->first();

        if (!$column) {
            throw new \Exception("A coluna não foi encontrada.");
        }

        $cardsByStage = $this->businessModel->where(['stage' => $column->id])->get();

        if ($cardsByStage->count() > 0) {
            $cardsByStage->each(function ($card) use ($data) {
                if ($card->index >= $data['index']) {
                    $card->index++;
                }
                $card->save();
            });
        }

        $businessUpdate = [
            'stage' => $column->id,
            'index' => $data['index']
        ];

        if ($column->is_last) {
            $businessUpdate['state_business'] = 'fechado';
            $businessUpdate['closed_at'] = Date::now();
        } else {
            $payments = new BusinessPayments();
            $paymentList = $payments->where('business_id', $data['id'])->get();

            $paymentList->each(function ($payment) {
                $payment->status = 'pending';
                $payment->save();
            });

            $businessUpdate['state_business'] = 'aberto';
            $businessUpdate['closed_at'] = null;
        }

        $business = $this->businessModel->find($data['id']);
        $business->stage = $column->id;
        $business->index = $businessUpdate['index'];
        $business->state_business = $businessUpdate['state_business'];
        $business->closed_at = $businessUpdate['closed_at'];
        $business->closed_state = 'aberto';
        $business->closed_at = $businessUpdate['closed_at'];
        $business->save();

        $history = new KanbanHistory();
        $history->fill([
            'business_id' => $business->id,
            'kanban_id' => $business->business_kanban_id,
            'kanban_column_id' => $business->stage,
        ]);
        $history->save();

        if ($business->acId) {
            $this->acService->updateDeal($business);
        }

        return $business;
    }

    public function reorderBusinessKanbanColumnIndex($businessKanbanId, $fromIndex, $toIndex)
    {
        $columnToMove = $this->businessKanbanColumnsModel
            ->where('business_kanban_id', $businessKanbanId)
            ->where('index', $fromIndex)
            ->first();

        if (!$columnToMove) {
            throw new \Exception("A coluna a ser movida não foi encontrada.");
        }

        $columnsToUpdate = $this->businessKanbanColumnsModel
            ->where('business_kanban_id', $businessKanbanId)
            ->whereBetween('index', [$fromIndex, $toIndex])
            ->get();

        foreach ($columnsToUpdate as $column) {
            if ($column->index == $fromIndex) {
                $column->index = $toIndex;
            } else {
                $column->index--;
            }
            $column->save();
        }

        $columnToMove->index = $toIndex;
        $columnToMove->save();

        return $columnToMove;
    }
}
