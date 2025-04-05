<?php

namespace App\Modules\Workers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Workers\Enums\TypeEnum;
use App\Modules\Tables\Services\TableService;

class WorkerRequest extends FormRequest
{
    private $tableService;

    public function rules()
    {
        $this->tableService = new TableService();

        $sometimes = '';

        $typeRules = [
            'required',
            Rule::in(TypeEnum::getAll())
        ];

        $nifRules = [
            'nullable',
            'numeric',
            $this->route('id')
                ? $this->uniqueRule('nif')->ignore($this->route('id'))
                : $this->uniqueRule('nif')
        ];

        $tableRules = [
            'required',
            Rule::in($this->tableService->getIdsByType($this->type))
        ];

        $usernameRules = [
            'nullable',
            'max:50',
            $this->route('id')
                ? $this->uniqueRule('username')->ignore($this->route('id'))
                : $this->uniqueRule('username')
        ];

        $emailRules = [
            in_array($this->type, ['operators', 'staff']) ? 'required' : 'nullable',
            'email',
            'max:50',
            $this->route('id')
                ? $this->uniqueRule('email')->ignore($this->route('id'))
                : $this->uniqueRule('email')
        ];

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';

            $typeRules = [
                'sometimes',
                ...$typeRules
            ];

            $tableRules = [
                'sometimes',
                ...$tableRules
            ];
        }

        return [
            'name' => $sometimes .'required|max:255',
            'phone' => $sometimes .'nullable|string',
            'nif' => $nifRules,
            'postal_code' => $sometimes .'nullable|max:8',
            'locality' => $sometimes .'nullable|max:255',
            'social_denomination' => $sometimes .'nullable|max:30',
            'responsible_name' => $sometimes .'nullable|max:255',
            'responsible_phone' => $sometimes .'nullable|string',
            'notes' => $sometimes .'nullable|max:255',
            'antecedence' => $sometimes .'nullable|max:255',
            'address' => $sometimes .'nullable|max:255',
            'email' => $emailRules,
            'username' => $usernameRules,
            'table_id' => $tableRules,
            'type' => $typeRules
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
            'phone' => 'Telefone',
            'nif' => 'NIF',
            'username' => 'Utilizador',
            'email' => 'Email',
            'postal_code' => 'Código Postal',
            'address' => 'Morada',
            'locality' => 'Localidade',
            'notes' => 'Observações',
            'type' => 'Tipo',
            'table_id' => 'Tabela'
        ];
    }

    private function uniqueRule($field)
    {
        return Rule::unique('bms_workers', $field)->where(function ($query) {
            $query->whereNull('deleted_at');
        });
    }
}