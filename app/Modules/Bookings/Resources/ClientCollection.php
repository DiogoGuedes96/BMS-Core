<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Bookings\Resources\ClientResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
