<?php

namespace App\Modules\Business\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ActiveCampaign\Services\ActiveCampaignService;
use App\Modules\Business\Models\BusinessFollowup;
use App\Modules\Business\Models\BusinessNotes;
use App\Modules\Business\Requests\CreateBusinessFollowupRequest;
use App\Modules\Business\Requests\CreateBusinessNotesRequest;
use App\Modules\Business\Requests\EditBusinessFollowupRequest;
use Illuminate\Http\Request;

class BusinessFollowupController extends Controller
{

    /** @var ActiveCampaignService */
    private $acService;

    public function __construct()
    {
        $this->acService = new ActiveCampaignService();
    }

    public function list(Request $request, $businessId)
    {
        try {
            $activeNotes = BusinessFollowup::where('business_id', $businessId)
                ->with('responsible')
                ->with('createdBy')
                ->where('completed', false)
                ->orderByRaw('CONCAT(date, " ", time) asc')
                ->get();

            $inactiveNotes = BusinessFollowup::where('business_id', $businessId)
                ->with('responsible')
                ->with('createdBy')
                ->where('completed', 1)
                ->orderByRaw('CONCAT(date, " ", time) desc')
                ->get();



            return response()->json([
                'data' => ['active' => $activeNotes, 'inactive' => $inactiveNotes],
                'message' => 'Successfully retrieved all notes',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve notes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function create(CreateBusinessFollowupRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['date'] = explode('T', $validatedData['date'])[0];
            $businessFollowup = BusinessFollowup::create($validatedData);

            $task = $this->acService->createOrUpdateDealFollowup($businessFollowup);
            if (!empty($task)) {
                $businessFollowup->acId = $task->id;
                $businessFollowup->save();
            }

            return response()->json([
                'data' => $businessFollowup,
                'message' => 'Successfully created note',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $businessFollowup = BusinessFollowup::find($id);

            if (!empty($businessFollowup->acId)) {
                $this->acService->deleteDealFollowup($businessFollowup);
            }

            $businessFollowup->delete();

            return response()->json([
                'message' => 'Successfully deleted note',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsCompleted($id)
    {
        $businessFollowup = BusinessFollowup::find($id);
        $businessFollowup->update(['completed' => true]);

        if (!empty($businessFollowup->acId)) {
            $this->acService->createOrUpdateDealFollowup($businessFollowup);
        }

        return response()->json([
            'data' => $businessFollowup,
            'message' => 'Successfully edit followup',
        ]);
    }

    public function edit(EditBusinessFollowupRequest $request, $id)
    {

        $businessFollowup = BusinessFollowup::find($id);
        $businessFollowup->update($request->all());

        if (!empty($businessFollowup->acId)) {
            $this->acService->createOrUpdateDealFollowup($businessFollowup);
        }

        return response()->json([
            'data' => $businessFollowup,
            'message' => 'Successfully edit followup',
        ]);
    }
}
