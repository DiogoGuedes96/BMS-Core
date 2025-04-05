<?php

namespace App\Modules\Bookings\Enums;

class StatusBookingEnum
{
    const APPROVED = 'approved';
    const CANCELED = 'canceled';
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const REFUSED = 'refused';

    public static function getAll()
    {
        return [
            self::APPROVED,
            self::CANCELED,
            self::DRAFT,
            self::PENDING,
            self::REFUSED
        ];
    }
}
