<?php

namespace App\Modules\Bookings\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Modules\Bookings\Enums\CarTypeEnum;
use App\Modules\Bookings\Enums\ChargeEnum;

class BookingServiceRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        $number_people = ($this->number_adults ?? 0) + ($this->number_children ?? 0);

        $carTypeRules = [
            'nullable',
            Rule::in($number_people <= 4 ? CarTypeEnum::SMALL : CarTypeEnum::LARGE)
        ];

        $chargeRules = [
            'required',
            Rule::in(ChargeEnum::getAll())
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';

            $chargeRules[] = 'sometimes';
        }

        return [
            'start' => $sometimes .'required|date',
            'hour' => $sometimes .'required',
            'number_adults' => $sometimes .'nullable|numeric|min:0',
            'booster' => $sometimes .'nullable|numeric|min:0',
            'car_type' => $carTypeRules,
            'value' => $sometimes .'required|numeric',
            'charge' => $chargeRules,
            'commission' => $sometimes .'nullable|numeric',
            'booking_id' => $sometimes .'required|numeric',
            'service_type_id' => $sometimes .'required|numeric',
            'service_state_id' => $sometimes .'required|numeric',
            'vehicle_id' => $sometimes .'nullable',
            'vehicle_text' => $sometimes .'nullable',
            'pickup_location_id' => $sometimes .'required_if:pickup_location,null',
            'pickup_location' => $sometimes .'required_if:pickup_location_id,null|string',
            'pickup_zone_id' => $sometimes .'required_if:pickup_zone,null',
            'pickup_zone' => $sometimes .'required_if:pickup_zone_id,null|string',
            'pickup_address' => $sometimes .'nullable|max:255',
            'dropoff_location_id' => $sometimes .'required_if:dropoff_location,null',
            'dropoff_location' => $sometimes .'required_if:dropoff_location_id,null|string',
            'dropoff_zone_id' => $sometimes .'required_if:dropoff_zone,null',
            'dropoff_zone' => $sometimes .'required_if:dropoff_zone_id,null|string',
            'dropoff_address' => $sometimes .'nullable|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'start' => 'Data de Serviço',
            'hour' => 'Hora de Serviço',
            'number_adults' => 'Número adultos',
            'booster' => 'Número booster',
            'service_type_id' => 'Tipo de Serviço',
            'service_state_id' => 'Estado',
            'car_type' => 'Tipo de carro',
            'vehicle_id' => 'Viatura',
            'vehicle_text' => 'Viatura',
            'pickup_location_id' => 'Local de Pick-up',
            'pickup_location' => 'Local de Pick-up',
            'pickup_zone_id' => 'Zona de Pick-up',
            'pickup_zone' => 'Zona de Pick-up',
            'pickup_address' => 'Morada de Pick-up',
            'dropoff_location_id' => 'Local de Drop off',
            'dropoff_location' => 'Local de Drop off',
            'dropoff_zone_id' => 'Zona de Drop off',
            'dropoff_zone' => 'Zona de Drop off',
            'dropoff_address' => 'Morada de Drop off',
            'value' => 'Valor',
            'charge' => 'Cobrança',
            'commission' => 'Comissão',
        ];
    }
}