<?php

namespace tests\unit\services;

use app\services\ProductCsvServiceInterface;
use Exception;
use Yii;

class ProductCsvServiceTest extends \Codeception\Test\Unit
{

    public function testSuccessRead()
    {
        $service = Yii::createObject(ProductCsvServiceInterface::class);

        $records = $service->readFromCsv(self::getCsvPath());

        $this->assertNotNull($records);

        foreach ($records as $record) {
            $this->assertIsArray($record);
            break;
        }
    }

    public function testTryReadNotFoundFile()
    {
        $service = Yii::createObject(ProductCsvServiceInterface::class);

        $this->expectException(Exception::class);

        $service->readFromCsv('bad path');
    }

    private static function getCsvPath(): string
    {
        return codecept_data_dir() . '/products.csv';
    }

}