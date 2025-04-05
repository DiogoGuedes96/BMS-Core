<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingUpdateVoucherRequest extends FormRequest
{
    public function rules()
    {
        return [
            'company_name' => 'sometimes|required|max:255',
            'company_phone' => 'sometimes|required|max:20',
            'company_email' => 'sometimes|required|email|max:255',
            'client_name' => 'sometimes|required|max:255',
            'pax_group' => 'sometimes|required|numeric',
            'operator' => 'sometimes|required|max:255',
            'services' => 'sometimes|required|array',
            'services.*.id' => 'sometimes|required|numeric',
            'services.*.start' => 'sometimes|required|date',
            'services.*.hour' => 'sometimes|required',
            'services.*.pickup_location' => 'sometimes|required|max:255',
            'services.*.dropoff_location' => 'sometimes|required|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'company_name' => 'Nome da empresa',
            'company_phone' => 'Contacto',
            'company_email' => 'Email',
            'client_name' => 'Nome do cliente',
            'pax_group' => 'Pax grupo',
            'operator' => 'Operador',
            'services' => 'Serviços',
            'services.*.id' => 'ID do serviço',
            'services.*.start' => 'Data',
            'services.*.hour' => 'Hora',
            'services.*.pickup_location' => 'Pick-up',
            'services.*.dropoff_location' => 'Drop-off'
        ];
    }
}