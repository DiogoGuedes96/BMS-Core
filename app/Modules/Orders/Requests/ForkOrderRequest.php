<?php

namespace App\Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForkOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'orderId' => 'required|numeric',
            'orderProducts' => 'required|array',
            'orderProducts.*' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'orderId.required' => 'The `orderId` field is required.',
            'orderId.numeric' => 'The `orderId` must be a numeric value.',
            'orderProducts.required' => 'At least one `orderProduct` is required.',
            'orderProducts.array' => 'The `orderProducts` must be an array.',
            'orderProducts.*.required' => 'All `orderProducts` must have a value.',
            'orderProducts.*.numeric' => 'The `orderProducts` must be numeric values.',
        ];
    }
}
