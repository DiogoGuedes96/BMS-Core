<?php

namespace App\Modules\Business\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Business\Requests\CreateColumnKanbanRequest;
use App\Modules\Business\Services\BusinessKanbanService;
use Illuminate\Http\Request;

class BusinessKanbanController extends Controller
{
    /**
     * @var BusinessKanbanService
     */
    protected BusinessKanbanService $kanbanService;

    public function __construct()
    {
        $this->kanbanService = new BusinessKanbanService();
    }

    public function list(Request $request)
    {
        $user = $request->user();

        $businessKanbans = $this->kanbanService->listBusinessKanbanAndColumns(null, $request->all(), $user);

        return response()->json([
            'data' => $businessKanbans,
            'message' => 'List all business kanbans',
        ]);
    }

    public function listTypes(Request $request)
    {
        $businessKanbans = $this->kanbanService->listBusinessKanbans();

        return response()->json([
            'data' => $businessKanbans,
            'message' => 'List all business kanbans types',
        ]);
    }

    public function listOne(Request $request, $type)
    {
        $user = $request->user();

        $businessKanbans = $this->kanbanService->listBusinessKanbanAndColumns($type, $request->all(), $user);

        return response()->json([
            'data' => $businessKanbans,
            'message' => 'List One business kanbans',
        ]);
    }

    public function create(CreateColumnKanbanRequest $request)
    {
        try {
            $this->kanbanService->createBusinessKanban($request->all());

            return response()->json([
                'data' => [],
                'message' => 'Business kanban created',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $businessKanban = $this->kanbanService->updateBusinessKanbanColumn($id, $request->all());

        return response()->json([
            'data' => $businessKanban,
            'message' => 'Business kanban updated',
        ]);
    }

    public function delete(Request $request, $id)
    {
        $businessKanban = $this->kanbanService->deleteBusinessKanbanColumn($id);

        return response()->json([
            'data' => $businessKanban,
            'message' => 'Business kanban deleted',
        ]);
    }

    public function move(Request $request)
    {
        $businessKanban = $this->kanbanService->moveCardInBusinessKanban($request->all());

        return response()->json([
            'data' => $businessKanban,
            'message' => 'Business kanban sorted',
        ]);
    }

    public function moveColumns(Request $request)
    {
        $businessKanban = $this->kanbanService->moveColumnInBusinessKanban($request->all());

        return response()->json([
            'data' => $businessKanban,
            'message' => 'Business kanban sorted',
        ]);
    }

    public function reorder(Request $request, $id)
    {
        // $businessKanban = $this->kanbanService->reorderBusinessKanbanColumnIndex($id);

        return response()->json([
            // 'data' => $businessKanban,
            'message' => 'Business kanban sorted',
        ]);
    }
}
