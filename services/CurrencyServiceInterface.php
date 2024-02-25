<?php

namespace app\services;

use Decimal\Decimal;
use Exception;

interface CurrencyServiceInterface
{
    /**
     * @param string $from currency code
     * @param string $to currency code
     * @throws Exception
     */
    public function convert(string $from, string $to, Decimal $value): Decimal;
}
