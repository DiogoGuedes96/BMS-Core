<?php

namespace App\Modules\Users\Helpers;

class TransformFieldsHelper
{
    public static function alias($value)
    {
        $alias = preg_replace('~[^\\pL\d]+~u', '-', $value);  
        $alias = trim($alias, '-');
        $alias = iconv('utf-8', 'ASCII//IGNORE//TRANSLIT', $alias);   
        $alias = strtolower(trim($alias));
        $alias = preg_replace('~[^-\w]+~', '', $alias);

        return $alias;
    }
}
