<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Bookings\Enums\StatusBookingEnum;
use Illuminate\Validation\Rule;

class BookingChangeStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(StatusBookingEnum::getAll())
            ],
            'status_reason' => [
                Rule::requiredIf(fn () => in_array(
                    $this->status,
                    [StatusBookingEnum::CANCELED, StatusBookingEnum::REFUSED]
                ))
            ]
        ];
    }

    public function attributes()
    {
        return [
            'status_reason' => 'Reason'
        ];
    }
}