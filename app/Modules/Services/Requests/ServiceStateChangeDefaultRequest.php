<?php

namespace App\Modules\Services\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStateChangeDefaultRequest extends FormRequest
{
    public function rules()
    {
        return [
            'is_default' => 'required|boolean'
        ];
    }

    public function attributes()
    {
        return [
            'is_default' => 'Valor padrão'
        ];
    }
}