<?php

namespace App\Modules\ServiceScheduling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceSchedulingRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'reason' => 'required|string',
            'additional_note' => 'nullable|string',
            'transport_feature' => 'required|string',
            'origin_address' => 'required|string',
            'destiny_address' => 'required|string',
            'is_back_service' => 'required',
            'vehicle' => 'nullable|string',
            'tat_1' => 'nullable|string',
            'tat_2' => 'nullable|string',
            'companion' => 'nullable|boolean',
            'payment_mode' => 'nullable|string',
            'total_value' => 'nullable|numeric',
            'transport_justification' => 'nullable|string',
            'patients_status' => 'required|string',
            'service_type' => 'nullable|string',
            'client' => 'nullable|integer',
            'patient_id' => 'required|integer',
        ];

        if ($this->post("is_repeat_schedule")) {
            $rules["repeat_time"] = 'required|date_format:H:i';
            $rules["repeat_days"] =  'required';
            $rules["repeat_days.*"] =  'required|string';

            if ($this->post("repeat_finish_by") === "date") {
                $rules["repeat_final_date"] = 'required|date';
            }

            if ($this->post("repeat_finish_by") === "sessions") {
                $rules["repeat_number_sessions"] = "required|integer";
            }
        } else {
            $rule['schedule_date'] = 'required|date';
            $rule['schedule_time'] = 'required|date_format:H:i';
        }

        if ($this->method('post') != "PUT") {

            if ($this->post("is_back_service") === 'yes') {
                $rules["back_service_origin_address"] = 'required|string';
                $rules["back_service_destiny_address"] = 'required|string';
            }
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'reason' => 'Motivo',
            'additional_note' => 'Nota adicional',
            'transport_feature' => 'Característica de transporte',
            'schedule_date' => 'Data de agendamento',
            'schedule_time' => 'Hora de agendamento',
            'origin_address' => 'Endereço de origem',
            'destiny_address' => 'Endereço de destino',
            'is_back_service' => 'Serviço de retorno',
            'vehicle' => 'Veículo',
            'client' => 'Cliente',
            'tat_1' => 'TAT1',
            'tat_2' => 'TAT2',
            'companion' => 'Companheiro',
            'payment_mode' => 'Modo de pagamento',
            'total_value' => 'Valor total',
            'transport_justification' => 'Justificação de transporte',
            'patients_status' => 'Estado do utente',
            'patient_id' => 'Identificador do Utente',
            'service_type' => 'Tipo de serviço',
            'uploads' => 'Ficheiros',
            'repeat_time' => 'Hora da repetição',
            'repeat_days' => 'Periodicidade',
            'repeat_days.*' => 'Dia da repetição',
            'repeat_final_date' => 'Data final de repetição',
            'repeat_number_sessions' => 'Número de sessões de repetição',
            'back_service_origin_address' => 'Endereço de origem do serviço de retorno',
            'back_service_destiny_address' => 'Endereço de destino do serviço de retorno'
        ];
    }
}
