<?php

namespace App\Modules\Services\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Services\Resources\ServiceResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
