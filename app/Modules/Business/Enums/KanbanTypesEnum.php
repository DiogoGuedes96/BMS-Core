<?php

namespace App\Modules\Business\Enums;

class KanbanTypesEnum
{
    const COACHING_BUSINESS_CLUB = 'COACHING_BUSINESS_CLUB';
    const DIAGNOSIS = 'DIAGNOSIS';

    public static function getType($type)
    {

        switch ($type) {
            case 'COACHING_BUSINESS_CLUB':
                return self::COACHING_BUSINESS_CLUB;
            case 'DIAGNOSIS':
                return self::DIAGNOSIS;
            default:
                return null;
        }
    }

    public static function getLabel($type)
    {

        switch ($type) {
            case 'COACHING_BUSINESS_CLUB':
                return 'clube de empresários';
            case 'DIAGNOSIS':
                return 'diagnóstico';
            default:
                return null;
        }
    }

    public static function getTypeValue($type)
    {
        switch (strtolower($type)) {
            case 'clube de empresários':
                return 'COACHING_BUSINESS_CLUB';
            case 'diagnóstico':
                return 'DIAGNOSIS';
            case 'coaching': //O cliente chama no AC dele o kanban diagnostico de coaching. Essa condição é apenas para servir o ambiente dele e não tem nenhuma referencia com esse valor no nosso sistema.
                return 'DIAGNOSIS';
            default:
                return null;
        }
    }
}
