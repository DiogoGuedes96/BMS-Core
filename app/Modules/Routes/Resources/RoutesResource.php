<?php

namespace App\Modules\Routes\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoutesResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($route) {
                return [
                    'id' => $route->id,
                    'from_zone_id' => $route->from_zone_id,
                    'from_zone' => $route->fromZone->name,
                    'to_zone_id' => $route->to_zone_id,
                    'to_zone' => $route->toZone->name,
                    'active' => $route->active,
                ];
            })
        ];
    }
}
