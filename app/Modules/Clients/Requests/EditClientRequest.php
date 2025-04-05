<?php

namespace App\Modules\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditClientRequest extends FormRequest
{
    public function rules()
    {
        $clientId = $this->input('id');

        return [
            'name'                 => 'required|string|max:255',
            'email'                => "nullable|string|email|unique:clients,email,{$clientId},id|max:50",
            'type'                 => 'required|string|max:25',
            'address'              => 'required|string|max:255',
            'nif'                  => 'nullable|integer|max:999999999',
            'status'               => 'required|boolean',
            'phone'                => 'required|integer|max:9999999999999',
            'responsibles'         => 'nullable|array',
            'responsibles.*.name'  => 'required|string|max:255',
            'responsibles.*.phone' => 'required|integer|max:9999999999999',
        ];
    }

    public function attributes()
    {
        return [
            'name'                     => 'Nome do cliente',
            'email'                    => 'Email do cliente',
            'type'                     => 'Tipo do cliente',
            'address'                  => 'Endereço do cliente',
            'nif'                      => 'NIF do cliente',
            'phone'                    => 'Telefone do cliente',
            'status'                   => 'Status do cliente',
            'responsibles'             => 'Responsaveis do cliente',
            'responsibles.*.name'  => 'Nome do responsável',
            'responsibles.*.phone' => 'Telefone do responsável',
        ];
    }
}