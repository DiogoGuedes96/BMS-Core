<?php

namespace App\Modules\Schedule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListRemindersByDatesFromUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dates'   => 'required|array',
            'dates.*' => 'date_format:d-m-Y',
        ];
    }

    public function messages()
    {
        return [
            'dates.required'      => 'The dates field is required.',
            'dates.array'         => 'The dates field must be an array.',
            'dates.*.date_format' => 'The dates must be in the format d-m-Y.',
        ];
    }
}
