<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingServiceColumnsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'columns' => 'required|array'
        ];
    }

    public function attributes()
    {
        return [
            'columns' => 'Colunas'
        ];
    }
}
