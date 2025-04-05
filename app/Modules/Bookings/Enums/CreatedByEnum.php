<?php

namespace App\Modules\Bookings\Enums;

class CreatedByEnum
{
    const ATRAVEL = 'atravel';
    const OPERATOR = 'operator';

    public static function getAll()
    {
        return [
            self::ATRAVEL,
            self::OPERATOR
        ];
    }
}
