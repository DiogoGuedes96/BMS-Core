<?php

namespace App\Modules\Workers\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Tables\Resources\TableResource;
use App\Modules\Users\Resources\UserResource;

class WorkerResource extends JsonResource
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
                'antecedence' => $this->antecedence,
                'username' => $this->username,
                'email' => $this->email,
                'notes' => $this->notes,
                'active' => $this->active,
                'type' => $this->type,
                'user_id' => $this->user_id,
                'table_id' => $this->table_id,
                'vehicle_id' => $this->vehicle_id,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ],
            'relationships' => [
                'user' => new UserResource($this->user),
                'table' => new TableResource($this->table),
                'vehicle' => $this->vehicle
            ]
        ];
    }
}
