<?php

namespace app\services;

use Decimal\Decimal;
use CentralBankRussian\ExchangeRate\Converter;
use CentralBankRussian\ExchangeRate\CBRClient;
use CentralBankRussian\ExchangeRate\ExchangeRate;

class CurrencyService implements CurrencyServiceInterface
{
    /**
     * @inheritDoc
     */
    public function convert(string $from, string $to, Decimal $value): Decimal
    {
        $exchangeRate = new ExchangeRate(new CBRClient());

        $amountFloat = (new Converter($exchangeRate))->convert($value->toFloat(), $from, $to);

        $amountStr = strval($amountFloat);

        return new Decimal($amountStr);
    }
}
