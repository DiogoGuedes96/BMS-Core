<?php

namespace App\Modules\Bookings\Enums;

class ChargeEnum
{
    const CLIENT = 'client';
    const DRIVER = 'driver';
    const OPERATOR = 'operator';
    const PARTNER = 'partner';
    const BANK = 'bank';
    const COMPANY = 'company';
    const OTHERS = 'others';

    public static function getAll()
    {
        return [
            self::DRIVER,
            self::OPERATOR,
            self::OTHERS
        ];
    }
}
