<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserModulePermissionsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'module_permissions' => 'required|array',
            'module_permissions*module' => 'required',
            'module_permissions*permissions' => 'required|object'
        ];
    }

    public function attributes()
    {
        return [
            'module_permissions' => 'Permissões por módulo',
            'module_permissions*module' => 'Módulo',
            'module_permissions*permissions' => 'Permissões'
        ];
    }
}
