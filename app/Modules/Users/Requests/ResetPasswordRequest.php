<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function attributes()
    {
        return [
            'token' => 'Token',
            'email' => 'Email',
            'password' => 'Password'
        ];
    }
}
