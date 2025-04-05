<?php

namespace App\Modules\Tables\Enums;

class StatusEnum
{
    const ACTIVE = true;
    const INACTIVE = false;

    public static function getAll()
    {
        return [
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE
        ];
    }
}
