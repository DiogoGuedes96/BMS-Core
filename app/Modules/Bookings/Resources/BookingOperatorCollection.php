<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookingOperatorCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Bookings\Resources\BookingOperatorResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
