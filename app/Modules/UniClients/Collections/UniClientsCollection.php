<?php

namespace App\Modules\UniClients\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UniClientsCollection extends ResourceCollection
{
    public $collects = 'App\Modules\UniClients\Resources\UniClientsResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
