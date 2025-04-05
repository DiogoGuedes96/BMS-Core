<?php

namespace App\Modules\UniClients\Resources;

use App\Modules\Users\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UniClientsResource extends JsonResource
{
    public function toArray($request)
    {
        if (!empty($this->open)) {
            $business = $this->businesses()
                ->with(['stage', 'businessKanban', 'referrer', 'closer', 'coach', 'product'])
                ->where('closed_state', '!=', 'perdido')
                ->where('state_business', '!=', 'fechado')
                ->get();
        } else {
            $business = $this->businesses()
                ->with(['stage', 'businessKanban', 'referrer', 'closer', 'coach', 'product'])
                ->where('closed_state', '!=', 'perdido')
                ->get();
        }

        return [
            'data' => [
                'id' => $this->id,
                'email' => $this->email,
                'phone' => $this->phone,
                'type' => $this->type,
                'name' => $this->name,
                'organization' => $this->organization,
                'type_business' => $this->type_business,
                'status' => $this->status,
                'referencer' => UserResource::make($this->whenLoaded('referrer')),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
                'deleted_at' => $this->updated_at->format('Y-m-d H:i:s'),
                'has_business' => (empty($business) || !empty($business) && count($business) === 0) ? false : true,
                'business' => $business
            ]
        ];
    }
}
