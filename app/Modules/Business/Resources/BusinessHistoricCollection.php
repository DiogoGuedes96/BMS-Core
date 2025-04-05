<?php

namespace App\Modules\Business\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BusinessHistoricCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Business\Resources\BusinessHistoricResource';

    public function toArray($request)
    {
        return $this->collection;
    }
}
