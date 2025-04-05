<?php

namespace App\Modules\Workers\Enums;

class TypeEnum
{
    const OPERATORS = 'operators';
    const SUPPLIERS = 'suppliers';
    const STAFF = 'staff';

    public static function getAll()
    {
        return [
            self::OPERATORS,
            self::SUPPLIERS,
            self::STAFF
        ];
    }
}
