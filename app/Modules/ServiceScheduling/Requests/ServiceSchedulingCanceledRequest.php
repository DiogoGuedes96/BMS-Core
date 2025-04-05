<?php

namespace App\Modules\ServiceScheduling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceSchedulingCanceledRequest extends FormRequest
{
    public function rules()
    {
        return [
            'reason' => 'required|string|max:255',
            'client_patient' => 'required|in:client,patient',
            'name' => 'required|string|max:255',
            'canceled_through' => 'in:call,email',
            'checkbox' => 'required|array',
            'checkbox.*' => 'required|integer'
        ];
    }

    public function attributes(){
        return [
            'reason' => 'Motivo',
            'client_patient' => 'Client/Patient',
            'name' => 'Name',
            'canceled_through' => 'Cancelado através',
            'checkbox' => 'checkbox',
        ];
    }
}
