<?php

namespace App\Modules\Workers\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkerCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Workers\Resources\WorkerResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
