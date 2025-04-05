<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingOperatorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'client_name' => $this->client_name,
                'client_email' => $this->client_email,
                'client_phone' => $this->client_phone,
                'value' => $this->value,
                'pax_group' => $this->pax_group,
                'start_date' => $this->start_date,
                'hour' => $this->hour,
                'pickup_location' => $this->pickup_location,
                'dropoff_location' => $this->dropoff_location,
                'created_by' => $this->created_by,
                'additional_information' => $this->additional_information,
                'status_reason' => $this->status_reason,
                'status' => $this->status,
                'reference' => $this->reference,
                'operator_id' => $this->operator_id,
                'pickup_location_id' => $this->pickup_location_id,
                'dropoff_location_id' => $this->dropoff_location_id,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ],
            'relationships' => [
                'operator' => new OperatorResource($this->operator)
            ]
        ];
    }
}
