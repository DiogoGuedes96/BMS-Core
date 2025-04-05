<?php

namespace App\Modules\Routes\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PagedZonesResource extends ResourceCollection
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
            }),
            'pagination' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ]
        ];
    }
}
