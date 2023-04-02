<?php

namespace App\Core\Utils\Strings;

use NumberFormatter;

class NumberToText
{
    public static function ToText($number)
    {
        $formatter = new NumberFormatter(
            "fr",
            NumberFormatter::SPELLOUT
        );

        return $formatter->format($number);
    }
}