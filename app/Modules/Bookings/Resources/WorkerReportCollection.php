<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkerReportCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Bookings\Resources\WorkerReportResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
