<?php

namespace App\Modules\Business\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BusinessPaymentResponsibleCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Business\Resources\BusinessPaymentResponsibleResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
