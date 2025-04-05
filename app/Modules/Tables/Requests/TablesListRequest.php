<?php

namespace App\Modules\Tables\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Tables\Enums\TypeEnum;

class TablesListRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => [
                'required',
                Rule::in(TypeEnum::getAll())
            ]
        ];
    }

    public function attributes()
    {
        return [
            'type' => 'Tipo'
        ];
    }
}