<?php

namespace App\Enums;

enum GameType: int
{
    case GROUP = 1;
    case QUARTERFINAL = 2;
    case SEMIFINAL = 3;
    case FINAL = 4;

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}