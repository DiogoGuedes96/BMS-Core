<?php

namespace App\Modules\Services\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceStateCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Services\Resources\ServiceStateResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
