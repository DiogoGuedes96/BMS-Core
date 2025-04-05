<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTotalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'total' => $this->resource
        ];
    }
}
