<?php

namespace App\Modules\Business\Requests;

use Illuminate\Foundation\Http\FormRequest;


class CreateBusinessNotesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'numeric',
            'business_id' => 'required|numeric',
            'created_by' => 'required|numeric',
            'content' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'business_id' => 'Negócio',
            'created_by' => 'Criado por',
            'content' => 'Conteudo',
        ];
    }
}
