<?php

namespace App\Core\Utils\Strings;

use NumberFormatter;

class FormatCurrency
{
    public static function format($amount, $currency = "Ar")
    {
        // ! the dutch (Germany) format is more close to the Malagasy format than the "mg-MG" itself
        $formatter = new NumberFormatter(
            "de-DE",
            NumberFormatter::CURRENCY
        );

        // ! so we replace € to the actual currency
        return str_replace(
            "€",
            $currency,
            $formatter->formatCurrency($amount, "EUR")
        );
    }
}