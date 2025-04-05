<?php

namespace App\Modules\Schedule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DelayBmsReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reminder_id'                         => 'required|numeric|exists:bms_reminder,id',
            'reminder_delay'                      => 'nullable|array',
            'reminder_delay.time_delay'           => 'required_with:reminder_delay|date_format:H:i',
            'reminder_delay.remember_time_delay'  => 'required_with:reminder_delay|date_format:H:i',
            'reminder_delay.remember_label_delay' => 'required_with:reminder_delay|string',
            'reminder_delay.date'                 => 'required_with:reminder_delay|date',
        ];
    }

    public function messages()
    {
        return [
            'reminder_id.required'                              => 'The reminder ID is required.',
            'reminder_id.numeric'                               => 'The reminder ID must be a numeric value.',
            'reminder_id.exists'                                => 'The given reminder_id does not exist.',
            'reminder_delay.array'                              => 'The reminder delay must be an array.',
            'reminder_delay.time_delay.required_with'           => 'The time delay is required when reminder delay is present.',
            'reminder_delay.time_delay.date_format'             => 'The time delay must be in H:i format.',
            'reminder_delay.remember_time_delay.required_with'  => 'The remember time delay is required when reminder delay is present.',
            'reminder_delay.remember_time_delay.date_format'    => 'The remember time delay must be in H:i format.',
            'reminder_delay.remember_label_delay.required_with' => 'The remember label delay is required when reminder delay is present.',
            'reminder_delay.remember_label_delay.string'        => 'The remember label delay must be a string.',
            'reminder_delay.date.required_with'                 => 'The date is required when reminder delay is present.',
            'reminder_delay.date.date'                          => 'The date must be a valid date.',
        ];
    }
}
