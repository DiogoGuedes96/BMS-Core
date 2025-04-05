<?php

namespace App\Modules\Business\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ActiveCampaign\Services\ActiveCampaignService;
use App\Modules\Business\Models\BusinessNotes;
use App\Modules\Business\Requests\CreateBusinessNotesRequest;
use Illuminate\Http\Request;

class BusinessNotesController extends Controller
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
            $businessNotes = BusinessNotes::where('business_id', $businessId)
                ->with('createdBy')
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'data' => $businessNotes,
                'message' => 'Successfully retrieved all notes',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve notes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function create(CreateBusinessNotesRequest $request)
    {
        try {
            $data = $request->all();

            if (!empty($data['id'])) {
                $businessNotes = BusinessNotes::find($data['id']);
                $businessNotes->update($data);
            } else {
                $businessNotes = BusinessNotes::create($data);
            }

            $note = $this->acService->createOrUpdateDealNote($businessNotes);
            if (empty($businessNotes->acId) && !empty($note)) {
                $businessNotes->acId = $note->id;
                $businessNotes->save();
            }

            return response()->json([
                'data' => $businessNotes,
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
            $businessNotes = BusinessNotes::find($id);

            if ($businessNotes->acId) {
                $this->acService->deleteDealNote($businessNotes);
            }

            $businessNotes->delete();

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
}
