<?php

namespace App\Modules\Bookings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'social_denomination' => $this->social_denomination,
                'nif' => $this->nif,
                'responsible_name' => $this->responsible_name,
                'responsible_phone' => $this->responsible_phone,
                'address' => $this->address,
                'postal_code' => $this->postal_code,
                'locality' => $this->locality,
                'tabela_id' => $this->tabela_id,
                'antecedence' => $this->antecedence,
                'username' => $this->username,
                'email' => $this->email,
                'notes' => $this->notes,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ]
        ];
    }
}
