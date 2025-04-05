<?php

namespace App\Modules\ServiceScheduling\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchedulingServiceCanceledDetailsResource extends JsonResource
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
            'canceled_reason' => $this->canceled_reason,
            'canceled_name' => $this->canceled_name,
            'canceled_client_patient' => $this->canceled_client_patient,
            'canceled_through' => $this->canceled_through
        ];
    }
}
