<?php

namespace App\Modules\Vehicles\Enums;

class GroupEnum
{
    const SMALL = 'small';
    const MEDIUM = 'medium';
    const LARGE = 'large';

    public static function getAll()
    {
        return [
            self::SMALL,
            self::LARGE
        ];
    }
}
