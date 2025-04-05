<?php

namespace App\Modules\Schedule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBmsReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'week_days'                           => 'required_without_all:month_day,start_date,year_day|array',
            'week_days.*'                         => 'numeric|between:1,7',
            'month_day'                           => 'required_without_all:week_days,start_date,year_day|numeric|max:31',
            'year_day'                            => 'required_without_all:week_days,start_date,week_days|numeric|max:366',
            'start_date'                          => 'required_without_all:week_days,month_day,year_day|date',
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
            'week_days.array'                                   => 'The week days must be an array.',
            'week_days.*.numeric'                               => 'The week day must be an numeric.',
            'week_days.*.between'                               => 'The week day must be between 1 and 8.',
            'month_day.numeric'                                 => 'The month day must be a numeric value.',
            'month_day.max'                                     => 'The month day cannot be greater than 31. A month only has at max 31 days',
            'year_day.numeric'                                  => 'The year day must be a numeric value.',
            'year_day.max'                                      => 'The year day cannot be greater than 366. A year only has 366 days at max',
            'start_date.required_without_all'                   => 'The start date is required if none of the week days, month day, or year day are provided.',
            'start_date.date'                                   => 'The start date must be a valid date.',
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
            'reminder_delay.array'                              => 'The reminder delay must be an array.',
            'reminder_delay.time_delay.required_with'           => 'The reminder delay time is required.',
            'reminder_delay.time_delay.date_format'             => 'The reminder delay time must be in the H:i format.',
            'reminder_delay.remember_time_delay.required_with'  => 'The reminder delay remember time is required.',
            'reminder_delay.remember_time_delay.date_format'    => 'The reminder delay remember time must be in the H:i format.',
            'reminder_delay.remember_label_delay.required_with' => 'The reminder delay remember label is required.',
            'reminder_delay.remember_label_delay.string'        => 'The reminder delay remember label must be a string.',
            'reminder_delay.date.required_with'                 => 'The reminder delay date is required.',
            'reminder_delay.date.date'                          => 'The reminder delay date must be a valid date.',
        ];
    }
}
