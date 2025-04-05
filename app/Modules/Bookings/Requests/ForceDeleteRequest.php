<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForceDeleteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|array',
        ];
    }
}