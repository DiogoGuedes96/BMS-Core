<?php

namespace App\Modules\Calls\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrRemoveCallsOnCacheRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'call_id' => 'required|integer|min:1|exists:asterisk_calls,id',
        ];
    }

    public function attributes()
    {
        return [
            'call_id' => 'ID da Chamada',
        ];
    }
}