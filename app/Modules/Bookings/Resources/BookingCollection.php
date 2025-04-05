<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookingCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Bookings\Resources\BookingResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
