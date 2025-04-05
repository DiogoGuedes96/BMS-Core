<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookingServiceCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Bookings\Resources\BookingServiceResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
