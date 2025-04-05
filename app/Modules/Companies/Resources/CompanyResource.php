<?php

namespace App\Modules\Companies\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        $imgUrl = $this->img_url ? asset(Storage::url($this->img_url)) : null;

        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'img_url' => $imgUrl,
                'notification_time' => $this->notification_time,
                'automatic_notification' => $this->automatic_notification,
                'api_key_maps' => $this->api_key_maps,
            ]
        ];
    }
}
