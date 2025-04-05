<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:50|unique:users,email,' . $id,
            'phone' => [
                Rule::requiredIf(!$id && env('BMS_CLIENT') == 'UNI'),
                'min:9',
                'max:11'
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
            'email' => 'Email',
            'phone' => 'Contacto'
        ];
    }
}
