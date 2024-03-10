<?php

namespace App\Enums;

enum ReactionType: string
{
    case THUMBS_UP = 'THUMBS_UP';
    case THUMBS_DOWN = 'THUMBS_DOWN';

    public static function getDefaults()
    {
        $defaults = [];
        foreach (self::cases() as $case) {
            $defaults[$case->value] = 0;
        }

        return $defaults;
    }
}
