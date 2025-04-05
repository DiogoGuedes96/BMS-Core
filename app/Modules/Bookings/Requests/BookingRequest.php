<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';
        }

        return [
            'client_name' => $sometimes .'required|max:255',
            'client_email' => $sometimes .'nullable|email|max:255',
            'client_phone' => $sometimes .'nullable|max:20',
            'reference' => $sometimes .'nullable|max:10',
            'value' => $sometimes .'required|numeric',
            'pax_group' => $sometimes .'required|numeric',
            'operator_id' => $sometimes .'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'client_name' => 'Cliente',
            'client_email' => 'Email',
            'client_phone' => 'Telefone',
            'reference' => 'Referência',
            'value' => 'Valor',
            'pax_group' => 'Pax grupo',
            'operator_id' => 'Operador'
        ];
    }
}