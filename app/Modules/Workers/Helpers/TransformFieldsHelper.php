<?php

namespace App\Modules\Workers\Helpers;

class TransformFieldsHelper
{
    public static function postalCode($postalCode)
    {
        $postalCode = str_replace('-', '', $postalCode);
        return substr($postalCode, 0, 4) . substr($postalCode, 4);
    }

    public static function password($password)
    {
        return bcrypt($password);
    }
}
