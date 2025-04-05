<?php

namespace App\Modules\AmbulanceCrew\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AmbulanceCrew\Models\AmbulanceGroup;
use App\Modules\AmbulanceCrew\Requests\AmbulanceGroupRequest;
use App\Modules\AmbulanceCrew\Resources\AmbulanceGroupResource;
use App\Modules\AmbulanceCrew\Resources\AmbulanceGroupWithCrewResource;
use App\Modules\AmbulanceCrew\Services\AmbulanceGroupService;
use Illuminate\Http\Request;
use Throwable;

class AmbulanceGroupController extends Controller
{
    private $ambulanceGroupService;
    public function __construct()
    {
        $this->ambulanceGroupService = new AmbulanceGroupService();
    }
    public function listAllAmbulanceGroup(Request $request)
    {
        try {
            $crew = $this->ambulanceGroupService->listAllAmbulanceGroup($request);
            return (AmbulanceGroupWithCrewResource::collection($crew))
                 ->response()->setStatusCode(200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant list the crews member',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function newAmbulanceGroup(AmbulanceGroupRequest $request) {
        try {
            return $this->ambulanceGroupService->newAmbulanceGroup($request->all());
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant create a crew member',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function editAmbulanceGroup(AmbulanceGroupRequest $request, AmbulanceGroup $ambulanceGroup) {
        try {
            return $this->ambulanceGroupService->editAmbulanceGroup($request->all(), $ambulanceGroup);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant edit a crew member',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function delAmbulanceGroup(AmbulanceGroup $ambulanceGroup) {
        try {
            return $this->ambulanceGroupService->delAmbulanceGroup($ambulanceGroup);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant delete a crew member',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
