<?php

namespace App\Modules\Schedule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListEventsByDatesFromUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dates'   => 'array',
            'dates.*' => 'date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'dates.required'      => 'The dates field is required.',
            'dates.array'         => 'The dates field must be an array.',
            'dates.*.date_format' => 'The dates must be in the format Y-m-d.',
        ];
    }
}
