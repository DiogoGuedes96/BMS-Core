<?php

namespace App\Modules\Routes\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AllZonesResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'active' => $zone->active,
                ];
            })
        ];
    }
}
