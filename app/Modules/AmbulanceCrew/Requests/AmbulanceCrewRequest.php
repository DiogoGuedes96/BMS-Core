<?php

namespace App\Modules\AmbulanceCrew\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmbulanceCrewRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'job_title' => ['nullable', 'string'],
            'contract_date' => ['nullable', 'date']
        ];

        if ($this->isMethod('post')) {
            $rules['nif'] = "nullable | integer | unique:ambulance_crew,nif";
            $rules['driver_license'] = "nullable | string | max:20 | unique:ambulance_crew,driver_license";
            $rules['contract_number'] = "nullable | integer | unique:ambulance_crew,contract_number";
            $rules['phone_number'] = "required | integer | unique:ambulance_crew,phone_number";
        }

        if ($this->isMethod('put')) {
            $ambulanceCrewId = $this->route('ambulanceCrew')->id ;
            $rules['nif'] = "nullable | integer | unique:ambulance_crew,nif,{$ambulanceCrewId},id";
            $rules['driver_license'] = "nullable | string | max:20 | unique:ambulance_crew,driver_license, {$ambulanceCrewId},id";
            $rules['contract_number'] = "nullable | integer | unique:ambulance_crew,contract_number, {$ambulanceCrewId},id";
            $rules['phone_number'] = "required | integer | unique:ambulance_crew,phone_number, {$ambulanceCrewId},id";
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
            'email' => 'E-mail',
            'address' => 'Morada',
            'status' => 'Status',
            'nif' => 'NIF',
            'driver_license' => 'Carta de Condução',
            'contract_number' => 'Número de Contrato',
            'phone_number' => 'Número de Telefone',
        ];
    }
}
