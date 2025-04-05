<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:50|unique:users',
            'password' => 'required|min:6',
            'phone' => [
                Rule::requiredIf(env('BMS_CLIENT') == 'UNI'),
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
            'password' => 'password',
            'phone' => 'Contacto',
        ];
    }
}
