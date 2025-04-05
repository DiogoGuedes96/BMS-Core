<?php

namespace App\Modules\Clients\Resources;

use App\Modules\Patients\Resources\PatientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            "id"      => $this->id,
            "name"    => $this->name,
            "email"   => $this->email,
            "type"    => $this->type,
            "address" => $this->address,
            "nif"     => $this->nif,
            "phone"   => $this->phone,
            "status"  => $this->status,

            "patients"   => PatientResource::collection($this->whenLoaded('patients')),

            $this->mergeWhen($this->clientResponsibles, [
                "responsibles" => ClientResponsibleResource::collection($this->clientResponsibles)
            ]),
        ];
    }
}
