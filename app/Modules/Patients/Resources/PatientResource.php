<?php

namespace App\Modules\Patients\Resources;

use App\Modules\Clients\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            "patientNumber" => $this->patient_number,
            "nif" => $this->nif,
            "birthday" => $this->birthday,
            "email" => $this->email,
            "address" => $this->address,
            "postalCode" => $this->postal_code,
            "postalCodeAddress" => $this->postal_code_address,
            "transportFeature" => $this->transport_feature,
            "patientObservations" => $this->patient_observations,
            "status" => $this->status,
            "phoneNumber" => $this->phone_number,
            "credits" => $this->credits,

            "clients"=> ClientResource::collection($this->whenLoaded('clients')),

            $this->mergeWhen($this->patientResponsible, [
                "responsibles" => PatientResponsibleResource::collection($this->patientResponsible)
            ]),
        ];
    }
}
