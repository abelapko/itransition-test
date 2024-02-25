<?php

namespace tests\unit\cases;

use app\cases\ImportProduct;
use app\cases\ImportProducts;
use app\services\ProductCsvService;
use app\services\ProductCsvServiceInterface;
use Closure;
use Iterator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yii;

class ImportProductsTest extends \Codeception\Test\Unit
{
    public function testSuccessImport()
    {
        $imported = $this->getModel()->exec('mocked');
        $this->assertCount(1, $imported);
    }

    private function getProductService(): ProductCsvServiceInterface
    {
        return $this->make(
            ProductCsvService::class,
            ['readFromCsv' => Closure::fromCallable([self::class, 'productsProvider'])]
        );
    }

    private static function productsProvider(): Iterator
    {
        // success product
        yield [
            'Product Code' => 'P00001',
            'Product Name' => 'Product Name',
            'Product Description' => 'Product Description',
            'Stock' => '20',
            'Cost in GBP' => '80.99',
            'Discontinued' => 'yes',
        ];
        // invalid product
        yield [
            'Product Code' => 'P00001',
            'Product Name' => 'Product Name',
            'Product Description' => 'Product Description',
            'Stock' => 'invalid',
            'Cost in GBP' => 'invalid',
            'Discontinued' => 'invalid',
        ];
    }

    private function getModel(): ImportProducts
    {
        return new ImportProducts(
            $this->getProductService(),
            Yii::createObject(EventDispatcherInterface::class),
            Yii::createObject(ImportProduct::class)
        );
    }
}