<?php

namespace App\Modules\Schedule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBmsEventReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reminder'                     => 'required|array',
            'reminder.id'                  => 'nullable|exists:bms_schedule_event,id',
            'reminder.title'               => 'required|string|max:255',
            'reminder.description'         => 'nullable|string|max:255',
            'reminder.client_phone'        => 'required|string',
            'reminder.client_name'         => 'required|string|max:255',
            'reminder.notes'               => 'nullable|string',
            'reminder.date'                => 'required|date',
            'reminder.time'                => 'required|date_format:H:i',
            'reminder.delay'               => 'required|array',
            'reminder.delay.*'             => 'required|integer',
            'reminder.recurrency_type'     => 'required|string|max:45',
            'reminder.status'              => 'required|string',
            'reminder.client_id'           => 'exists:bms_clients,id',
            'reminder.recurrency_week_days'   => 'nullable|array',
            'reminder.recurrency_week_days.*' => 'integer|digits_between:1,7',
            'reminder.recurrency_week'        => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'reminder.required'                           => 'The reminder details are required.',
            'reminder.title.required'                     => 'The reminder title is required.',
            'reminder.title.string'                       => 'The reminder title must be a string.',
            'reminder.title.max'                          => 'The reminder title may not be greater than :max characters.',
            'reminder.description.required'               => 'The reminder description is required.',
            'reminder.description.string'                 => 'The reminder description must be a string.',
            'reminder.description.max'                    => 'The reminder description may not be greater than :max characters.',
            'reminder.client_phone.required'              => 'The client phone is required for the reminder.',
            'reminder.client_phone.string'                => 'The client phone must be a string.',
            'reminder.client_name.required'               => 'The client name is required for the reminder.',
            'reminder.client_name.string'                 => 'The client name must be a string.',
            'reminder.client_name.max'                    => 'The client name may not be greater than :max characters.',
            'reminder.notes.required'                     => 'The notes are required for the reminder.',
            'reminder.notes.string'                       => 'The notes must be a string.',
            'reminder.date.required'                      => 'The date is required for the reminder.',
            'reminder.date.date'                          => 'The date must be a valid date for the reminder.',
            'reminder.time.required'                      => 'The time is required for the reminder.',
            'reminder.time.date_format'                   => 'The time must be in H:i format for the reminder.',
            'reminder.delay.required'                     => 'The delay is required for the reminder.',
            'reminder.delay.array'                        => 'The delay must be a valid array of strings for the reminder.',
            'reminder.delay.*.integer'                    => 'Each delay must be an integer .',
            'reminder.recurrency_type.required'           => 'The recurrency type is required for the reminder.',
            'reminder.recurrency_type.string'             => 'The recurrency type must be a string for the reminder.',
            'reminder.recurrency_type.max'                => 'The recurrency type may not be greater than :max characters for the reminder.',
            'reminder.status.required'                    => 'The status is required for the reminder.',
            'reminder.status.string'                      => 'The status must be a string for the reminder.',
            'reminder.client_id.exists'                   => 'The selected client is invalid for the reminder.',
            'reminder.recurrency_week_days.array'            => 'The recurrency week days must be an array for the event.',
            'reminder.recurrency_week_days.*.integer'        => 'Each recurrency week day must be an integer for the event.',
            'reminder.recurrency_week_days.*.digits_between' => 'Each recurrency week day must be between 1 and 7 for the event.',
            'reminder.recurrency_week.integer'               => 'The recurrency week must be an integer for the event.',
            'reminder.recurrency_week.nullable'              => 'The recurrency week must be an integer for the event.',
        ];
    }
}
