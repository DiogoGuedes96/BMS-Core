<?php

namespace App\Modules\ServiceScheduling\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchedulingServicePatientResource extends JsonResource
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
            'id' => $this->id,
            "name" => $this->name,
            "phoneNumber" => $this->phone_number,
            "patientNumber" => $this->patient_number,
            "email" => $this->email,
            "nif" => $this->nif,
            "credits" => $this->credits
        ];
    }
}
