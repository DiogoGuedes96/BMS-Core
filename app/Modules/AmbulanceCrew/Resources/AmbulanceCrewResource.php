<?php

namespace App\Modules\AmbulanceCrew\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmbulanceCrewResource extends JsonResource
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
            "email" => $this->email,
            "phoneNumber" => $this->phone_number,
            "nif" => $this->nif,
            "driverLicense" => $this->driver_license,
            "contractDate" => $this->contract_date,
            "contractNumber" => $this->contract_number,
            "jobTitle" => $this->job_title,
            "address" => $this->address,
            "status" => $this->status,
        ];
    }
}
