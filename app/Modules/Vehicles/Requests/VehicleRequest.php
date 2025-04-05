<?php

namespace App\Modules\Vehicles\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Vehicles\Enums\GroupEnum;

class VehicleRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        $groupRules = [
            'nullable',
            Rule::in(GroupEnum::getAll())
        ];

        $licenseRules = [
            'required',
            'max:8',
            $this->route('id')
                ? $this->uniqueRule('license')->ignore($this->route('id'))
                : $this->uniqueRule('license')
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';

            $groupRules = [
                'sometimes',
                ...$groupRules
            ];
        }

        return [
            'brand' => $sometimes .'required|max:25',
            'model' => $sometimes .'required|max:30',
            'group' => $groupRules,
            'license' => $licenseRules,
            'km' => $sometimes .'nullable|numeric',
            'max_capacity' => $sometimes .'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'brand' => 'Marca',
            'model' => 'Modelo',
            'group' => 'Grupo',
            'license' => 'Matrícula',
            'km' => 'Km Actuais',
            'max_capacity' => 'Lotação Máxima'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_vehicles', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}