<?php

namespace App\Modules\Routes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ZonesCreate extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('bms_zones', 'name')->where(function ($query) {
                    $query->whereNull('deleted_at');
                })
            ]
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome'
        ];
    }
}