<?php

namespace App\Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $today = Carbon::now()->format('Y-m-d');

        return [
            'values'                     => 'required|array',
            'values.zona.id'             => 'numeric',
            'values.zona.value'          => 'required|string',
            'values.request'             => 'required|string',
            'values.notes'               => 'nullable|string',
            'values.delivery_date'       => [
                'required',
                'date',
                'after_or_equal:' . $today,
            ],
            'values.delivery_period'     => 'required|string',
            'values.priority'            => 'required|boolean',
            'values.address'             => 'nullable|string',
            'values.address.addressId'   => 'nullable|numeric',
            'values.caller_phone'        => 'nullable|numeric',
            'orderData'                  => 'required|array',
            'orderData.products'         => 'required|array',
            'orderData.products.*.id'    => 'required|numeric',
            'orderData.products.*.notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'values.zona.required'                => 'The zona field is required.',
            'values.zona.string'                  => 'The zona field must be a string.',
            'values.request.required'             => 'The request field is required.',
            'values.request.string'               => 'The request field must be a string.',
            'values.notes.string'                 => 'The notes field must be a string.',
            'values.delivery_date.required'       => 'The delivery date field is required.',
            'values.delivery_date.date'           => 'The delivery date field must be a valid date.',
            'values.delivery_date.after_or_equal' => 'The delivery date must be today or a future date.',
            'values.delivery_period.required'     => 'The delivery period field is required.',
            'values.delivery_period.string'       => 'The delivery period field must be a string.',
            'values.priority.required'            => 'The priority field is required.',
            'values.priority.boolean'             => 'The priority field must be a boolean.',
            'values.address.string'               => 'The address field must be a string.',
            'values.addressId.numeric'            => 'The address field must be a numeric value.',
            'values.caller_phone.numeric'         => 'The caller phone field must be a numeric value.',
            'orderData.required'                  => 'The orderData field is required.',
            'orderData.array'                     => 'The orderData field must be an array.',
            'orderData.products.required'         => 'The products field is required.',
            'orderData.products.array'            => 'The products field must be an array.',
            'orderData.products.*.id.required'    => 'The product ID field is required.',
            'orderData.products.*.id.numeric'     => 'The product ID field must be a numeric value.',
            'orderData.products.*.notes.string'   => 'The notes field must be a string.',
        ];
    }
}
