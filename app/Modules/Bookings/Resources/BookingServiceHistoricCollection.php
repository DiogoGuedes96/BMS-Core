<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookingServiceHistoricCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Bookings\Resources\BookingServiceHistoricResource';

    public function toArray($request)
    {
        return $this->collection;
    }
}
