<?php

namespace App\Modules\Routes\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Routes\Resources\LocationResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
