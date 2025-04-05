<?php

namespace App\Modules\Tables\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Tables\Enums\TypeEnum;

class TableRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        $typeRules = [
            'required',
            Rule::in(TypeEnum::getAll())
        ];

        $nameRules = [
            'required',
            'max:25',
            $this->route('id')
                ? $this->uniqueRule('name')->ignore($this->route('id'))
                : $this->uniqueRule('name')
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';
            $typeRules = [
                'sometimes',
                ...$typeRules
            ];
        }

        return [
            'name' => $nameRules,
            'type' => $typeRules
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Título',
            'type' => 'Tipo'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_tables', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}