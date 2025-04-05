<?php

namespace App\Modules\Tables\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TableRoutesCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Tables\Resources\TableRoutesResource';

    public function toArray($request)
    {
        return $this->collection;
    }
}
