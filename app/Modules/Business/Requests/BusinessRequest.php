<?php

namespace App\Modules\Business\Requests;

use App\Modules\Business\Enums\CommissionMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class BusinessRequest extends FormRequest
{
    public function rules()
    {
        $sometimes = '';

        if (in_array(\strtolower($this->method()), ['put', 'patch'])) {
            $sometimes = 'sometimes|';
        }

        return [
            'client_id' => $sometimes . 'required|numeric|exists:uni_clients,id',
            'name' => $sometimes . 'required|max:255',
            'value' => $sometimes . 'max:20',
            // 'type_product' => $sometimes . 'required|max:255',
            'business_kanban_id' => $sometimes . 'required|numeric|exists:business_kanban,id',
            'stage' => $sometimes . 'required|numeric|exists:business_kanban_columns,id',
            // 'state_business' => $sometimes .'required|boolean',
            'referrer_id' => $sometimes . 'required|numeric|exists:users,id',
            'referrer_commission' => $sometimes . 'nullable|max:20',
            'referrer_commission_method' => ['nullable', Rule::in(CommissionMethodEnum::getAll())],
            'coach_id' => $sometimes . 'nullable|numeric|exists:users,id',
            'coach_commission' => $sometimes . 'nullable|max:20',
            'coach_commission_method' => ['nullable', Rule::in(CommissionMethodEnum::getAll())],
            'closer_id' => $sometimes . 'nullable|numeric|exists:users,id',
            'closer_commission' => $sometimes . 'nullable|max:20',
            'closer_commission_method' => ['nullable', Rule::in(CommissionMethodEnum::getAll())],
            'description' => 'nullable|max:600',
            'product_id' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'client_id' => 'Cliente',
            'name' => 'Nome',
            'value' => 'Valor',
            'product_id' => 'Produto',
            'business_kanban_id' => 'Tipo de Funil',
            'stage' => 'Etapa',
            'state_business' => 'Status',
            'referrer_id' => 'Referenciador',
            'referrer_commission' => 'Comissão do Referenciador',
            'referrer_commission_method' => 'Tipo de Pagamento do Referenciador',
            'coach_id' => 'Business Coach',
            'coach_commission' => 'Comissão do Coach',
            'coach_commission_method' => 'Tipo de Pagamento do Coach',
            'closer_id' => 'Closer',
            'closer_commission' => 'Comissão do Closer',
            'closer_commission_method' => 'Tipo de Pagamento do Closer',
            'description' => 'Descrição'
        ];
    }
}
