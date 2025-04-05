<?php

namespace App\Modules\Tables\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TableCollection extends ResourceCollection
{
    public $collects = 'App\Modules\Tables\Resources\TableResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
