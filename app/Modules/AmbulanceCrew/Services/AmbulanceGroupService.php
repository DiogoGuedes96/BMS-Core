<?php

namespace App\Modules\AmbulanceCrew\Services;

use App\Modules\AmbulanceCrew\Models\AmbulanceCrew;
use App\Modules\AmbulanceCrew\Models\AmbulanceGroup;

class AmbulanceGroupService
{
    private $ambulanceGroup;
    private $ambulanceCrew;
    public function __construct()
    {
        $this->ambulanceGroup = new AmbulanceGroup();
        $this->ambulanceCrew = new AmbulanceCrew();
    }

    public function listAllAmbulanceGroup($request){

        $search = $request->get('search', '');

        $crew = $this->ambulanceGroup->with(['crew']);
        if ($search) {
            $crew = $crew->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }

        if(!$request->sorter){
            $crew = $crew->orderBy('created_at', 'desc');
        }

        $crew = $crew->when($request->sorter === 'ascend', function ($query) {
            return $query->orderBy('name', 'asc');
        })
        ->when($request->sorter === 'descend', function ($query) {
            return $query->orderBy('name', 'desc');
        })
        ->paginate($request->get('perPage') ?? 10);

        return $crew;
    }

    public function newAmbulanceGroup(array $data) {
        $group = $this->ambulanceGroup->create($data)->id;
        $this->ambulanceCrew->whereIn('id', $data['crew'])->update(["group_id" => $group]);
        return response()->json([
            'message' => 'Group created successfully',
        ]);
    }

    public function editAmbulanceGroup(array $data, $ambulanceGroup) {
        $crewMembers = $ambulanceGroup->crew;
        $crewIdsRemove = $crewMembers->filter(function ($item) use ($data) {
            return !in_array($item->id, $data["crew"]);
        })->pluck('id')->toArray();
        $this->ambulanceCrew->whereIn('id', $crewIdsRemove)->update(["group_id" => 0]);
        $this->ambulanceCrew->whereIn('id', $data["crew"])->update(["group_id" => $ambulanceGroup->id]);
        $ambulanceGroup->update(["name" => $data["name"]]);
        return response()->json([
            'message' => 'Group updated successfully',
        ]);
    }

    public function delAmbulanceGroup(AmbulanceGroup $ambulanceGroup) {
        $crewMembers = $ambulanceGroup->crew;
        $this->ambulanceCrew->whereIn('id', $crewMembers->pluck('id'))->update(["group_id" => 0]);
        $ambulanceGroup->delete();
        return response()->json([
            'message' => 'Group deleted successfully'
        ]);
    }
}
