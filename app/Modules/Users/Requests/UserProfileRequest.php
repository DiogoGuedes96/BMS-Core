<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => [
                'required',
                'max:20',
                $this->route('id')
                    ? Rule::unique('user_profile', 'description')->ignore($this->route('id'))
                    : Rule::unique('user_profile', 'description')
            ]
        ];
    }

    public function attributes()
    {
        return [
            'description' => 'Nome do perfil'
        ];
    }
}
