<?php

namespace App\Modules\Business\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BusinessPaymentCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Business\Resources\BusinessPaymentResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
