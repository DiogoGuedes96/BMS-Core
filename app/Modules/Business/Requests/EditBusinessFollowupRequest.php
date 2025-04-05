<?php

namespace App\Modules\Business\Requests;

use Illuminate\Foundation\Http\FormRequest;


class EditBusinessFollowupRequest extends FormRequest
{
    public function rules()
    {
        return [
            'business_id' => 'required|numeric',
            'created_by' => 'required|numeric',
            'responsible_id' => 'required|numeric',
            'title' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'is_important' => 'boolean',
        ];
    }

    public function attributes()
    {
        return [
            'business_id' => 'Negócio',
            'created_by' => 'Criado por',
            'responsible_id' => 'Responsável',
            'title' => 'Título',
            'date' => 'Data',
            'time' => 'Hora',
            'is_important' => 'Importante',
        ];
    }
}
