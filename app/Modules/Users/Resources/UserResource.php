<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "profile_id" => $this->profile_id,
            "phone" => $this->phone,
            "active" => $this->active,
            "first_access" => $this->first_access,
            "last_access" => $this->last_access,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            $this->mergeWhen($this->profile, [
                "profile" => UserProfileResource::make($this->profile)
            ])
        ];
    }
}
