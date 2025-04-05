<?php

namespace App\Modules\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrUpdateCompanyRequest extends FormRequest
{
    public function rules()
    {
        $companyId = $this->input('id');

        $imageRule = !$this->input('img_path')
            ? [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ]
            : 'nullable';

        return [
            'name' => 'required|max:255',
            'phone' => 'required|string|max:30',
            'email' => [
                'required',
                'email',
                Rule::unique('companies', 'email')->ignore($companyId),
            ],
            'image' => $imageRule,
            'notification_time' => 'nullable|date_format:H:i',
            'automatic_notification' => 'nullable|in:true,false',
        ];
    }
}
