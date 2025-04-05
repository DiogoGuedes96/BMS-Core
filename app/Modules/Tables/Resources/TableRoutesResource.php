<?php

namespace App\Modules\Tables\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TableRoutesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'route_id' => $this->pivot->route_id,
            'from_zone_id' => $this->from_zone_id,
            'to_zone_id' => $this->to_zone_id,
            'from_zone' => $this->fromZone->name,
            'to_zone' => $this->toZone->name,
            'pax14' => $this->pivot->pax14,
            'pax58' => $this->pivot->pax58,
            'active' => $this->active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
