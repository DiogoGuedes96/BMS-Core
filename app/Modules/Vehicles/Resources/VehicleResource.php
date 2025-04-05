<?php

namespace App\Modules\Vehicles\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'brand' => $this->brand,
                'model' => $this->model,
                'group' => $this->group,
                'license' => $this->license,
                'km' => $this->km,
                'max_capacity' => $this->max_capacity,
                'active' => $this->active ?? true,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ]
        ];
    }
}
