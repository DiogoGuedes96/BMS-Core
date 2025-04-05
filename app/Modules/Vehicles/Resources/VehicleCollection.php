<?php

namespace App\Modules\Vehicles\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VehicleCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Vehicles\Resources\VehicleResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
