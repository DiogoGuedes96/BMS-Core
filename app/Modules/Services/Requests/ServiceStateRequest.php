<?php

namespace App\Modules\Services\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceStateRequest extends FormRequest
{
    public function rules()
    {
        $nameRules = [
            'required',
            'max:20',
            $this->route('id')
                ? $this->uniqueRule('name')->ignore($this->route('id'))
                : $this->uniqueRule('name')
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $nameRules = [
                'sometimes',
                ...$nameRules
            ];
        }

        return [
            'name' => $nameRules
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome estado'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_service_states', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}