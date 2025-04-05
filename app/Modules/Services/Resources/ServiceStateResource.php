<?php

namespace App\Modules\Services\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceStateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'active' => $this->active,
                'is_default' => $this->is_default,
                'readonly' => $this->readonly,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ]
        ];
    }
}
