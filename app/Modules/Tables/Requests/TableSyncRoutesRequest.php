<?php

namespace App\Modules\Tables\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Tables\Enums\TypeEnum;

class TableSyncRoutesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'routes' => 'required|array',
            'routes.*.id' => 'required',
            'routes.*.pax14' => 'required|numeric',
            'routes.*.pax58' => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'routes' => 'Rotas',
            'routes.*.id' => 'ID da rota',
            'routes.*.pax14' => 'Valor Pax 1-4',
            'routes.*.pax58' => 'Valor Pax 5-8'
        ];
    }
}