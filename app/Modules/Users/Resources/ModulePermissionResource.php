<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModulePermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            "module" => $this->module,
            "label" => $this->label,
            "permissions" => $this->permissions ?? [],
            "created_at" => !empty($this->created_at) ? $this->created_at->format('Y-m-d H:i:s') : null,
            "updated_at" => !empty($this->updated_at) ? $this->updated_at->format('Y-m-d H:i:s') : null
        ];
    }
}
