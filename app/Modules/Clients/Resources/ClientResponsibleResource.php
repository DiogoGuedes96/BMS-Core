<?php
namespace App\Modules\Clients\Resources;

use App\Modules\Clients\Models\ClientResponsable;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResponsibleResource extends JsonResource
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
            "id"      => $this->id,
            "name"    => $this->name,
            "phone"   => $this->phone,
        ];
    }
}
