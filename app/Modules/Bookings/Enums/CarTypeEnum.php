<?php

namespace App\Modules\Bookings\Enums;

class CarTypeEnum
{
    const SMALL = 'small';
    const LARGE = 'large';

    public static function getAll()
    {
        return [
            self::SMALL,
            self::LARGE
        ];
    }
}
