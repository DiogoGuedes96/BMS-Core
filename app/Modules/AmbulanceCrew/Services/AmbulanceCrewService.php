<?php

namespace App\Modules\AmbulanceCrew\Services;

use App\Modules\AmbulanceCrew\Models\AmbulanceCrew;

class AmbulanceCrewService
{
    private $ambulanceCrew;
    public function __construct()
    {
        $this->ambulanceCrew = new AmbulanceCrew();
    }

    public function listAllAmbulanceCrew($request){

        $search = $request->get('search', '');

        $crew = $this->ambulanceCrew->with(['group']);
        if ($search) {
            $crew = $crew->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')->orWhere('nif', 'like', '%' . $search . '%');
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

    public function newAmbulanceCrew(array $data) {
        $this->ambulanceCrew->create($data);
        return response()->json([
            'message' => 'Crew created successfully',
        ]);
    }

    public function editAmbulanceCrew(array $data, AmbulanceCrew $ambulanceCrew) {
        $ambulanceCrew->update($data);
        return response()->json([
            'message' => 'Crew created successfully',
        ]);
    }

    public function delAmbulanceCrew (AmbulanceCrew $ambulanceCrew) {
        $ambulanceCrew->delete();
        return response()->json([
            'message' => 'Crew deleted successfully'
        ]);
    }
}

