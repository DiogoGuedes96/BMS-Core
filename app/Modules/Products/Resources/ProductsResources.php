<?php

namespace App\Modules\Products\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResources extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'value' => $this->value,
                'commission' => $this->commission,
                'coin' => $this->coin,
                'status' => $this->status,
                'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
                'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s') : null
            ]
        ];
    }
}
