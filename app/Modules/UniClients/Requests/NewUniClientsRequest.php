<?php

namespace App\Modules\UniClients\Requests;

use App\Modules\UniClients\Enums\StatusClientsEnum;
use App\Modules\UniClients\Enums\StatusTypeBusinessEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewUniClientsRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:255', 'unique:uni_clients'],
            'phone' => ['required', 'integer', 'max:9999999999999'],
            'type' => ['required', 'string', 'in:'.implode(',', StatusClientsEnum::getAll())],
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['string', 'max:255'],
            'referencer' => ['required', 'integer', 'exists:users,id'],
            'type_business' => ['string', 'in:'.implode(',',StatusTypeBusinessEnum::getAll())],
            'status' => ['required']
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'email' => 'E-mail',
            'phone' => 'Contacto',
            'type' => 'Tipo do contacto',
            'name' => 'Nome',
            'organization' => 'Organização',
            'referencer' => 'Referenciador',
            'type_business' => 'Tipo de negócio',
            'status' => 'Status'
        ];
    }
}
