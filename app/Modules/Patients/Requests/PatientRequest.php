<?php

namespace App\Modules\Patients\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'integer'],
            'postal_code_address' => ['required', 'string', 'max:255'],
            'transport_feature' => ['required', 'string', 'max:255'],
            'patient_observations' => ['required', 'string'],
            'status' => ['nullable', 'integer'],
            'patient_phone_number' => ['required', 'integer'],
            'entities' => ['nullable', 'array'],
            'entities.*' => ['required', 'integer', Rule::exists('clients', 'id')],
        ];

        if ($this->isMethod('post')) {
            $rules['nif'] = "required | integer | unique:patients,nif";
            $rules['patient_number'] = "required | integer | unique:patients,patient_number";
        }

        if ($this->isMethod('put')) {
            $patientId = $this->route('patient')->id ;
            $rules['nif'] = "required | integer | unique:patients,nif,{$patientId},id";
            $rules['patient_number'] = "required | integer | unique:patients,patient_number,{$patientId},id";
        }

        $dynamicRules = array_filter($this->all(), function (mixed $value, string $key): bool {
            return preg_match('/^phone_number_/', $key);
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($dynamicRules as $key => $value) {
            $i = substr($key, strrpos($key, '_') + 1);
            $rules["patient_responsible_$i"] = ['required', 'string', 'max:255'];
            $rules["phone_number_$i"] = ['required', 'integer'];
        }

        return $rules;
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        $atributes = [
            'name' => 'Nome',
            'patient_number' => 'Número utente',
            'nif' => 'NIF',
            'birthday' => 'Data de aniversário',
            'email' => 'Email',
            'address' => 'Morada',
            'postal_code' => 'Código postal',
            'postal_code_address' => 'Localidade',
            'transport_feature' => 'Característica do transporte',
            'patient_observations' => 'Observações',
            'patient_phone_number' => 'Telefone',
            'status' => 'Estado',
            'entities' => 'Entidades',
            'entities.*' => 'Entidade',
        ];

        $dynamicRules = array_filter($this->all(), function (mixed $value, mixed $key): bool {
            return preg_match('/^phone_number_/', $key);
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($dynamicRules as $key => $value) {
            $i = substr($key, strrpos($key, '_') + 1);
            $atributes["patient_responsible_$i"] = "$iº responsável";
            $atributes["phone_number_$i"] = "Contacto do $iº responsável";
        }

        return $atributes;
    }
}
