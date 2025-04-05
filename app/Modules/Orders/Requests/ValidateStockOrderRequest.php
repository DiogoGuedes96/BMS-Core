<?php

namespace App\Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateStockOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'required|string',
            'products' => 'required|array',
            'products.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'The status field is required.',
            'status.string' => 'The status must be a string.',
            'products.required' => 'The products field is required.',
            'products.array' => 'The products must be an array.',
            'products.*.integer' => 'Each product must be an integer.',
        ];
    }
}
