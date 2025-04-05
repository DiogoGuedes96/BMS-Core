<?php

namespace App\Modules\Workers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Workers\Enums\TypeEnum;

class WorkersListRequest extends FormRequest
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