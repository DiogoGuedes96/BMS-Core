<?php

namespace App\Modules\Business\Enums;

class CommissionMethodEnum
{
    const RECURRENT = 'recorrente';
    const THREE = '3x';
    const SIX = '6x';
    const TWELVE = '12x';
    const TOTALPAYMENT = 'pagamento total';
    const CLOSEPAYMENT = 'encerrar pagamento';

    public static function getAll()
    {
        return [
            self::RECURRENT,
            self::THREE,
            self::SIX,
            self::TWELVE,
            self::TOTALPAYMENT,
            self::CLOSEPAYMENT
        ];
    }
}
