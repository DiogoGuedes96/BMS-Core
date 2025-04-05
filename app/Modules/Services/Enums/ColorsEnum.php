<?php

namespace App\Modules\Services\Enums;

class ColorsEnum
{
    const NO_COLOR = '#ffffff';
    const YELLOW = '#f1dd1c';
    const DARK_YELLOW = '#e9b20c';
    const BLUE = '#4d73fe';
    const DARK_BLUE = '#4750ea';
    const CYAN = '#67bec3';
    const ORANGE = '#e59401';
    const DARK_ORANGE = '#e06400';
    const MAGENTA = '#eb2f96';
    const PINK = '#d24692';
    const PURPLE = '#6d2fd0';
    const GREEN = '#76c12b';
    const LIGHT_GREEN = '#acd826';
    const RED = '#d9421d';

    public static function getAll()
    {
        return [
            [ 'value' => self::NO_COLOR, 'label' => 'Sem cor' ],
            [ 'value' => self::YELLOW, 'label' => 'Amarelo' ],
            [ 'value' => self::DARK_YELLOW, 'label' => 'Amarelo Escuro' ],
            [ 'value' => self::BLUE, 'label' => 'Azul' ],
            [ 'value' => self::DARK_BLUE, 'label' => 'Azul Escuro' ],
            [ 'value' => self::CYAN, 'label' => 'Ciano' ],
            [ 'value' => self::ORANGE, 'label' => 'Laranja' ],
            [ 'value' => self::DARK_ORANGE, 'label' => 'Laranja Escuro' ],
            [ 'value' => self::MAGENTA, 'label' => 'Magenta' ],
            [ 'value' => self::PINK, 'label' => 'Rosa' ],
            [ 'value' => self::PURPLE, 'label' => 'Roxo' ],
            [ 'value' => self::GREEN, 'label' => 'Verde' ],
            [ 'value' => self::LIGHT_GREEN, 'label' => 'Verde Claro' ],
            [ 'value' => self::RED, 'label' => 'Vermelho' ]
        ];
    }
}
