<?php

namespace App\Modules\Feedback\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'patient_number' => ['nullable', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
            'feedbackWho' => ['required'],
            'feedbackWho.*.name' => ['required', 'string', 'max:50'],
            'date' => 'nullable | date',
            'time' => 'nullable | date_format:H:i',
            'observations' => ['required', 'string']
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
            'patient_number' => 'Número de Utente',
            'feedbackWho.*.name' => 'A quem', 
            'reason' => 'Motivo',
            'feedbackWho' => 'A quem',
            'date' => 'Data',
            'time' => 'Hora',
            'observations' => 'Observações'
        ];
    }
}
