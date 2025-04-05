<?php

namespace App\Modules\Calls\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCallRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id'       => 'required|integer|exists:asterisk_calls,id',
            'call_reason'   => 'nullable|string|max:255',
            'call_operator' => 'nullable|integer|exists:users,id',
            'status'        => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'id'       => 'Identificador da Chamada',
            'call_reason'   => 'Motivo da chamada',
            'call_operator' => 'Operador da Chamada',
            'status'        => 'Estado da Chamada',
        ];
    }
}
