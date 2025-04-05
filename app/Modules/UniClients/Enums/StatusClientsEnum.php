<?php

namespace App\Modules\UniClients\Enums;

class StatusClientsEnum
{
    const COMMERCIAL = 'commercial';
    const PERSONAL = 'personal';

    public static function getAll()
    {
        return [
            self::COMMERCIAL,
            self::PERSONAL,
        ];
    }
}
