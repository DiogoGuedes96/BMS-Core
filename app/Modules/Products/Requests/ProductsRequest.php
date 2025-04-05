<?php

namespace App\Modules\Products\Requests;

use App\Modules\Products\Enums\CoinProductsEnum;
use App\Modules\Products\Enums\StatusProductsEnum;
use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'value' => 'required|max:255',
            'commission' => 'nullable|max:255',
            'coin' => 'nullable|max:100|in:'.implode(',',CoinProductsEnum::getAll()),
            'status' => 'in:'.implode(',',StatusProductsEnum::getAll()),
        ];
    }

    public function attributes()
    {
        return [
            "name" => "Nome",
            "value" => "Valor",
            "commission" => "Comissão",
            "coin" => "Moeda",
            "status" => "Status",
        ];
    }
}