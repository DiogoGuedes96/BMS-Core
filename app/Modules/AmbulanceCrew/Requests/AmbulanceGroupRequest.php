<?php

namespace App\Modules\AmbulanceCrew\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmbulanceGroupRequest extends FormRequest
{
    public function rules()
    {

        $rules = [
            'crew' => 'required',
            'crew.*.id' => 'integer',
        ];

        if ($this->isMethod('post')) {
            $rules['name'] = "string | unique:ambulance_groups,name";
        }

        if ($this->isMethod('put')) {
            $ambulanceCrewId = $this->route('ambulanceGroup')->id ;
            $rules['name'] = "string | unique:ambulance_groups,name,{$ambulanceCrewId},id";
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
        ];
    }
}
