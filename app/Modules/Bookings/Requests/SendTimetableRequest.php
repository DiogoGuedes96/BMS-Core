<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendTimetableRequest extends FormRequest
{
    public function rules()
    {
        return [
            'conductor' => 'required|max:255',
            'email' => 'required|email|max:255',
            'date' => 'required|string',
            'timetable' => 'required|file|mimes:pdf',
        ];
    }
}