<?php

namespace App\Modules\Products\Enums;

class CoinProductsEnum
{
    const EURO = 'euro';
    const LIBRA = 'libra';
    const DOLAR = 'dolar';
    const REAL = 'real';

    public static function getAll()
    {
        return [
            self::EURO,
            self::LIBRA,
            self::DOLAR,
            self::REAL,
        ];
    }
}
