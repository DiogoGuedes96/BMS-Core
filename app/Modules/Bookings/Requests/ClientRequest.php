<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        $emailRules = [
            'required',
            'max:255',
            'email',
            $this->route('id')
                ? $this->uniqueRule('email')->ignore($this->route('id'))
                : $this->uniqueRule('email')
        ];

        $phoneRules = [
            'max:30',
            'nullable',
            $this->route('id')
                ? $this->uniqueRule('phone')->ignore($this->route('id'))
                : $this->uniqueRule('phone')
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';

            $emailRules[] = 'sometimes';
            $phoneRules[] = 'sometimes';
        }

        return [
            'name' => $sometimes .'required|max:255',
            'email' => $emailRules,
            'phone' => $phoneRules
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Cliente',
            'email' => 'Email',
            'phone' => 'Telefone'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_booking_clients', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}