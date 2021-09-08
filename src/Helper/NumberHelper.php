<?php
declare(strict_types=1);

namespace HPT\Helper;

class NumberHelper
{
    /**
     * Ze stringu vyparsuje ciselnou hodnotu a prevede na float
     * @param string $value
     * @return float
     */
    public function parseFloat(string $value): float
    {
        $value = str_replace(",", ".", $value);
        return (float)preg_replace("/[^0-9.]/", "", $value);
    }
}