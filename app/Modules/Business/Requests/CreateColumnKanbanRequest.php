<?php

namespace App\Modules\Business\Requests;

use App\Modules\Business\Enums\CommissionMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CreateColumnKanbanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:35',
            'color' => 'required|string',
            'kanban_type' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Nome',
            'color' => 'Cor',
            'kanban_type' => 'Kanban',
        ];
    }
}
