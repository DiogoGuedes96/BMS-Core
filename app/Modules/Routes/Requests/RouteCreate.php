<?php

namespace App\Modules\Routes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RouteCreate extends FormRequest
{
    public function rules()
    {
        return [
            'from_zone_id' => [
                'required',
                'exists:bms_zones,id',
                Rule::unique('bms_routes', 'from_zone_id')->where(function ($query) {
                    $query->where('to_zone_id', '=', $this->to_zone_id)
                        ->whereNull('deleted_at');
                })
            ],
            'to_zone_id' => 'required|exists:bms_zones,id',
        ];
    }

    public function messages()
    {
        return [
            'from_zone_id.required' => 'A zona de partida é campo obrigatório.',
            'from_zone_id.exists' => 'A zona de partida selecionada é inválida.',
            'from_zone_id.unique' => 'A combinação entre a zona de partida e zona de chegada já existe.',
            'to_zone_id.required' => 'A zona de chegada é campo obrigatório.',
            'to_zone_id.exists' => 'A zona de chegada selecionada é inválida.',
            'to_zone_id.unique' => 'A combinação entre a zona de chegada e zona de partida já existe.',
        ];
    }
}
