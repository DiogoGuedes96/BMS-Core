<?php

namespace App\Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'callerPhone'                     => 'nullable|numeric',
            'bmsClient'                       => 'nullable|numeric|exists:bms_clients,id',
            'orderNotes'                      => 'nullable|string|max:1024',
            'orderProducts'                   => 'required|array',
            'orderProducts.*.bms_product'     => 'required|numeric',
            'orderProducts.*.unit'            => 'required|string|max:255',
            'orderProducts.*.volume'          => 'required|string|max:255',
            'orderProducts.*.quantity'        => 'required|numeric',
            'orderProducts.*.price'           => 'required|numeric',
            'orderProducts.*.correctionPrice' => 'nullable|numeric',
            'orderProducts.*.discount'        => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
        'orderNotes.max'                          => 'The Notes field must not exceed 1024 characters.',
        'bmsClient.numeric'                       => 'The BMS client must be a numeric value.',
        'reminder_info.client_id.exists'          => 'The given client_id does not exist.',
        'callerPhone.numeric'                     => 'The caller phone must be a numeric value.',
        'orderProducts.required'                  => 'At least one orderProduct is required to create an Order.',
        'orderProducts.array'                     => 'The `orderProducts` must be an array.',
        'orderProducts.*.bms_product.required'    => 'The `bms_product` field is required for all order products.',
        'orderProducts.*.bms_product.numeric'     => 'The `bms_product` must be a numeric value for all order products.',
        'orderProducts.*.unit.required'           => 'The `unit` field is required for all order products.',
        'orderProducts.*.unit.string'             => 'The `unit` must be a string for all order products.',
        'orderProducts.*.volume.required'         => 'The `volume` field is required for all order products.',
        'orderProducts.*.volume.string'           => 'The `volume` must be a string for all order products.',
        'orderProducts.*.quantity.required'       => 'The `quantity` field is required for all order products.',
        'orderProducts.*.quantity.numeric'        => 'The `quantity` must be a numeric value for all order products.',
        'orderProducts.*.price.required'          => 'The `price` field is required for all order products.',
        'orderProducts.*.price.numeric'           => 'The `price` must be a numeric value for all order products.',
        'orderProducts.*.correctionPrice.numeric' => 'The `correctionPrice` must be a numeric value for all order products.',
        'orderProducts.*.discount.numeric'        => 'The `discount` must be a numeric value for all order products.',
        ];
    }
}
