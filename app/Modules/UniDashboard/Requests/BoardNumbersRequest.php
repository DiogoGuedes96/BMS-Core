<?php

namespace App\Modules\UniDashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoardNumbersRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => ['string'],
            'start_date' => ['string'],
            'end_date' => ['string'],
            'client' => ['string'],
            'referrer' => ['string'],
            'businessCoach' => ['string'],
            'closer' => ['string'],
        ];
    }
}
