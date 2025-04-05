<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "perPage" => $this->perPage(),
            "currentPage" => $this->currentPage(),
            "total" => $this->total(),
            "lastPage" => $this->lastPage()
        ];
    }
}
