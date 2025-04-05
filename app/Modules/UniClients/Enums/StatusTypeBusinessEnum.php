<?php

namespace App\Modules\UniClients\Enums;

class StatusTypeBusinessEnum
{
    const DIAGNOSTIC = 'diagnostic';
    const BUSINESS_CLUB = 'business_club';

    public static function getAll()
    {
        return [
            self::DIAGNOSTIC,
            self::BUSINESS_CLUB,
        ];
    }
}
