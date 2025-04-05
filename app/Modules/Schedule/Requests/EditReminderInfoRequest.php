<?php

namespace App\Modules\Schedule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditReminderInfoRequest extends FormRequest
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
            'reminder_info'                       => 'required|array',
            'reminder_info.name'                  => 'required_with:reminder_info|string',
            'reminder_info.date'                  => 'required_with:reminder_info|date',
            'reminder_info.time'                  => 'required_with:reminder_info|date_format:H:i',
            'reminder_info.remember_time'         => 'nullable|date_format:H:i',
            'reminder_info.remember_label'        => 'required_with:reminder_info|numeric',
            'reminder_info.client_id'             => 'nullable|required_without_all:reminder_info.client_phone,reminder_info.client_name|numeric|exists:bms_clients,id',
            'reminder_info.client_name'           => 'required_without:reminder_info.client_id|string',
            'reminder_info.client_phone'          => 'required_without:reminder_info.client_id|string',
            'reminder_info.notes'                 => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'reminder_id.required'                              => 'The reminder ID is required.',
            'reminder_id.numeric'                               => 'The reminder ID must be a numeric value.',
            'reminder_id.exists'                                => 'The given reminder_id does not exist.',
            'reminder_info.required'                            => 'The reminder info field is required.',
            'reminder_info.array'                               => 'The reminder info must be an array.',
            'reminder_info.name.required_with'                  => 'The reminder name is required.',
            'reminder_info.name.string'                         => 'The reminder name must be a string.',
            'reminder_info.date.required_with'                  => 'The reminder date is required.',
            'reminder_info.date.date'                           => 'The reminder date must be a valid date.',
            'reminder_info.time.required_with'                  => 'The reminder time is required.',
            'reminder_info.time.date_format'                    => 'The reminder time must be in the H:i format.',
            'reminder_info.remember_time.date_format'           => 'The reminder remember time must be in the H:i format.',
            'reminder_info.remember_label.required_with'        => 'The reminder remember label is required.',
            'reminder_info.remember_label.numeric'               => 'The reminder remember label must be a numeric.',
            'reminder_info.client_id.nullable'                  => 'The reminder client ID must be null or a numeric value.',
            'reminder_info.client_id.exists'                    => 'The given client_id does not exist.',
            'reminder_info.client_id.required_without_all'      => 'The reminder client ID is required when client name or client phone is not provided.',
            'reminder_info.client_id.numeric'                   => 'The reminder client ID must be a numeric value.',
            'reminder_info.client_name.required_without'        => 'The reminder client name is required when client ID is not provided.',
            'reminder_info.client_name.string'                  => 'The reminder client name must be a string.',
            'reminder_info.client_phone.required_without'       => 'The reminder client phone is required when client ID is not provided.',
            'reminder_info.client_phone.string'                 => 'The reminder client phone must be a string.',
            'reminder_info.notes.nullable'                      => 'The reminder notes must be null or a string.',
        ];
    }
}
