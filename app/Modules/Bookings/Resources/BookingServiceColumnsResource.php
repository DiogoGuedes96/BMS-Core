<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingServiceColumnsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => !empty($this->settings) && !empty($this->settings['service_columns'])
                ? $this->settings['service_columns']
                : []
        ];
    }
}
