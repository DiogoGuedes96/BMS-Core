<?php

namespace App\Modules\Products\Enums;

class StatusProductsEnum
{
    const ACTIVE = true;
    const INACTIVE = false;

    public static function getAll()
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }
}
