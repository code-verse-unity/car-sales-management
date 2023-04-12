<?php

namespace App\Core\Utils\Strings;

use DateTime;
use IntlDateFormatter;

class DateFormatter
{
    public static function format(
        DateTime $date,
        $format = "dd MMMM yyyy",
        $locale = "fr_FR",
        $currentTimeZone = $_ENV["CURRENT_TIMEZONE_NAME"]
    ) {
        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            $currentTimeZone,
            IntlDateFormatter::GREGORIAN,
            $format
        );

        return implode(
            " ",
            array_map(
                function ($value) {
                    return ucfirst($value);
                },
                explode(" ", $formatter->format($date))
            )
        );
    }
}
