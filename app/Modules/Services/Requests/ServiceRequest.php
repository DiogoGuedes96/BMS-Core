<?php

namespace App\Modules\Services\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Services\Enums\ColorsEnum;

class ServiceRequest extends FormRequest
{
    public function rules()
    {
        $nameRules = [
            'required',
            'max:25',
            $this->route('id')
                ? $this->uniqueRule('name')->ignore($this->route('id'))
                : $this->uniqueRule('name')
        ];

        $colorRules = [
            'nullable',
            Rule::in(array_map(function($color) {
                return $color['value'];
            }, ColorsEnum::getAll()))
        ];

        $uniqueColor = [];

        if ($this->filled('color') && $this->color != ColorsEnum::NO_COLOR) {
            $uniqueColor = [
                $this->route('id')
                    ? $this->uniqueRule('color')->ignore($this->route('id'))
                    : $this->uniqueRule('color')
            ];
        }

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $nameRules = [
                'sometimes',
                ...$nameRules
            ];

            $colorRules = [
                'sometimes',
                ...$colorRules
            ];
        }

        $colorRules = [
            ...$colorRules,
            ...$uniqueColor
        ];

        return [
            'name' => $nameRules,
            'color' => $colorRules
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome serviço',
            'color' => 'Cor'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_services', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}