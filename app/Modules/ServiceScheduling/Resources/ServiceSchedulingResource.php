<?php

namespace App\Modules\ServiceScheduling\Resources;

use App\Modules\ServiceScheduling\Models\ServiceSchedulingRepeatModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSchedulingResource extends JsonResource
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
            "reason" => $this->reason,
            "additional_note" => $this->additional_note,
            "patients_status" => $this->patients_status,
            "transport_feature" => $this->transport_feature,
            "service_type" => $this->service_type,
            "date" => $this->date,
            "time" => $this->time,
            "origin" => $this->origin,
            "destination" => $this->destination,
            "vehicle" => $this->vehicle,
            "license_plate" => $this->license_plate,
            "responsible_tats_1" => $this->responsible_tats_1,
            "responsible_tats_2" => $this->responsible_tats_2,
            "companion" => $this->companion,
            "companion_name" => $this->companion_name,
            "companion_contact" => $this->companion_contact,
            "transport_justification" => $this->transport_justification,
            "payment_method" => $this->payment_method,
            "total_value" => $this->total_value,
            "is_back_service" => $this->is_back_service,
            "associated_schedule" => $this->associated_schedule,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "repeat_id" => $this->repeat_id,
            $this->mergeWhen($this->patient, [
                "patient" => SchedulingServicePatientResource::make($this->patient)
            ]),
            $this->mergeWhen($this->client, [
                "client" => SchedulingServiceClientResource::make($this->client)
            ]),
            $this->mergeWhen($this->user, [
                "user" => SchedulingServiceUserResource::make($this->user)
            ]),
            $this->mergeWhen($this->parent, [
                "parent" => SchedulingServiceParentResource::make($this->parent)
            ]),
            $this->mergeWhen($this->uploads->isNotEmpty(), [
                "uploads" => ServiceSchedulingUploadResource::collection($this->uploads)
            ]),
            $this->mergeWhen($this->repeat, [
                "repeat" => ServiceSchedulingRepeatResource::make($this->repeat)
            ])
        ];
    }
}
