<?php

namespace App\Modules\AmbulanceCrew\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmbulanceGroupWithCrewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            $this->mergeWhen($this->crew, [
                "crew" => AmbulanceCrewResource::collection($this->crew)
            ]),
        ];
    }
}
