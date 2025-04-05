<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingOperatorRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';
        }

        return [
            'client_name' => $sometimes .'required|max:255',
            'client_email' => $sometimes .'required|email|max:255',
            'client_phone' => $sometimes .'nullable|max:20',
            'start_date' => $sometimes .'required|date',
            'hour' => $sometimes .'required',
            'pickup_location_id' => $sometimes .'required_if:pickup_location,null',
            'pickup_location' => $sometimes .'required_if:pickup_location_id,null|string',
            'dropoff_location_id' => $sometimes .'required_if:dropoff_location,null',
            'dropoff_location' => $sometimes .'required_if:dropoff_location_id,null|string',
            'value' => $sometimes .'required|numeric',
            'pax_group' => $sometimes .'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'client_name' => 'Client',
            'client_email' => 'Email',
            'client_phone' => 'Phone number',
            'reference' => 'nullable|max:10',
            'value' => 'Amount',
            'pax_group' => 'Number of people',
            'start_date' => 'Date',
            'hour' => 'Time',
            'pickup_location_id' => 'Pick-up local',
            'pickup_location' => 'Pick-up local',
            'dropoff_location_id' => 'Drop off local',
            'dropoff_location' => 'Drop off local'
        ];
    }
}