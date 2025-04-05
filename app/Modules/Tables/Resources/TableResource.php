<?php

namespace App\Modules\Tables\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'type' => $this->type,
                'active' => $this->active,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ],
            'relationships' => [
                'routes' => new TableRoutesCollection($this->routes)
            ]
        ];
    }
}
