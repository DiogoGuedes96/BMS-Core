<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileModuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'description' => $this->description,
            'active' => $this->active,
            "readonly" => $this->readonly,
            'total_users' => $this->users->count(),
            'created_at' => !empty($this->created_at) ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => !empty($this->updated_at) ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'module_permissions' => $this->userProfileModules->map(function ($modulePermission) {
                return [
                    'id' => $modulePermission->id,
                    'module' => $modulePermission->module,
                    'permissions' => $modulePermission->permissions ?? [],
                    'created_at' => !empty($modulePermission->created_at) ? $modulePermission->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => !empty($modulePermission->updated_at) ? $modulePermission->updated_at->format('Y-m-d H:i:s') : null
                ];
            })
        ];
    }
}
