<?php

namespace App\Modules\Routes\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'address' => $this->address,
                'lat' => $this->lat,
                'long' => $this->long,
                'reference_point' => $this->reference_point,
                'zone_id' => $this->zone_id,
                'active' => $this->active,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ],
            'relationships' => [
                'zone' => [
                    'id' => $this->zone->id,
                    'name' => $this->zone->name,
                    'active' => $this->zone->active
                ]
            ]
        ];
    }
}
