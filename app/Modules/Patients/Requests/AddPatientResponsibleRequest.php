<?php

namespace App\Modules\Patients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPatientResponsibleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id'                   => 'required|integer|exists:patients,id',
            'responsibles'         => 'nullable|array',
            'responsibles.*.name'  => 'required|string|max:255',
            'responsibles.*.phoneNumber' => 'required|integer|max:9999999999999',
        ];
    }

    public function attributes()
    {
        return [
            'id'                       => 'Identificador do utente',
            'responsibles'             => 'Responsaveis do utente',
            'responsibles.*.name'  => 'Nome do responsável',
            'responsibles.*.phoneNumber' => 'Telefone do responsável',
        ];
    }
}