<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:6|different:current_password',
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => 'Password atual',
            'new_password' => 'Novo password',
        ];
    }
}
