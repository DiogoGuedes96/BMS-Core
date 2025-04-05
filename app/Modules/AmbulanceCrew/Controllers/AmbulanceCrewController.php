<?php

namespace App\Modules\AmbulanceCrew\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AmbulanceCrew\Models\AmbulanceCrew;
use App\Modules\AmbulanceCrew\Requests\AmbulanceCrewRequest;
use App\Modules\AmbulanceCrew\Resources\AmbulanceCrewResource;
use App\Modules\AmbulanceCrew\Resources\AmbulanceCrewWithGroupsResource;
use App\Modules\AmbulanceCrew\Services\AmbulanceCrewService;
use Illuminate\Http\Request;
use Throwable;

use function Illuminate\Validation\Rules\message;

class AmbulanceCrewController extends Controller
{
    private $ambulanceCrewService;
    public function __construct()
    {
        $this->ambulanceCrewService = new AmbulanceCrewService();
    }
    public function listAllAmbulanceCrew(Request $request)
    {
        try {
            $crew = $this->ambulanceCrewService->listAllAmbulanceCrew($request);
            return (AmbulanceCrewWithGroupsResource::collection($crew))
                 ->response()->setStatusCode(200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant list the crews member',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function newAmbulanceCrew(AmbulanceCrewRequest $request) {
        try {
            return $this->ambulanceCrewService->newAmbulanceCrew($request->all());
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant create a crew member',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function editAmbulanceCrew(AmbulanceCrewRequest $request, AmbulanceCrew $ambulanceCrew) {
        try {
            return $this->ambulanceCrewService->editAmbulanceCrew($request->all(), $ambulanceCrew);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant edit a crew member',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function delAmbulanceCrew(AmbulanceCrew $ambulanceCrew) {
        try {
            return $this->ambulanceCrewService->delAmbulanceCrew($ambulanceCrew);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant delete a crew member',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
