<?php

namespace app\tests\unit\services;

use app\services\CurrencyServiceInterface;
use Decimal\Decimal;
use Exception;
use Yii;

class CurrencyServiceTest extends \Codeception\Test\Unit
{
    public function testSuccessConvert()
    {
        $service = Yii::createObject(CurrencyServiceInterface::class);

        $service->convert('USD', 'GBP', new Decimal(5));
    }

    public function testTryConvertWithInvalidSymbol()
    {
        $service = Yii::createObject(CurrencyServiceInterface::class);

        $this->expectException(Exception::class);

        $service->convert('invalid', 'GBP', new Decimal(5));
    }

}