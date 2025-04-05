<?php

namespace App\Modules\Routes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        $nameRules = [
            'required',
            'max:80',
            $this->route('id')
                ? $this->uniqueRule('name')->ignore($this->route('id'))
                : $this->uniqueRule('name')
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';

            $nameRules = [
                'sometimes',
                ...$nameRules
            ];
        }

        return [
            'name' => $nameRules,
            'address' => $sometimes .'required|max:255',
            'lat' => $sometimes .'required|numeric',
            'long' => $sometimes .'required|numeric',
            'zone_id' => $sometimes .'required'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Local',
            'address' => 'Morada',
            'lat' => 'Latitude',
            'long' => 'Longitude',
            'zone_id' => 'Zona'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_locations', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}