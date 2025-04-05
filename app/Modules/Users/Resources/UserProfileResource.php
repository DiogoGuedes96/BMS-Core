<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'description' => $this->description,
            'readonly' => $this->readonly,
            'active' => $this->active,
            'total_users' => $this->users->count(),
            'created_at' => !empty($this->created_at) ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => !empty($this->updated_at) ? $this->updated_at->format('Y-m-d H:i:s') : null
        ];
    }
}
