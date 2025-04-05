<?php

namespace App\Modules\Users\Enums;

class PermissionTypesEnum
{
    const WRITE = 'write';
    const READ = 'read';
    const NONE = 'none';
    const HAVE = 'have';

    public static function getAll()
    {
        return [
            self::WRITE => 'Escrita',
            self::READ => 'Leitura',
            self::NONE => 'Sem permissão'
        ];
    }

    public static function getSimplified()
    {
        return [
            self::HAVE => 'Tem permissão',
            self::NONE => 'Sem permissão'
        ];
    }
}
