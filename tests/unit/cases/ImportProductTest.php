<?php

namespace app\tests\unit\cases;

use app\cases\ImportProduct;
use app\dto\ProductCsv;
use DomainException;
use Yii;

class ImportProductTest extends \Codeception\Test\Unit
{
    /**
     * @dataProvider productsProvider
     */
    public function testImport(array $productData, bool $expectedImport, ?string $exceptionExpected)
    {
        $model = Yii::createObject(ImportProduct::class);

        if ($exceptionExpected) {
            $this->expectException($exceptionExpected);
        }

        $isImported = !! $model->exec(new ProductCsv($productData));

        $this->assertEquals($expectedImport, $isImported);
    }

    public function testImportDiscontinuedProduct()
    {
        $model = Yii::createObject(ImportProduct::class);

        $product = $model->exec(new ProductCsv([
            'code' => 'P00001',
            'name' => 'Product Name',
            'description' => 'Product Description',
            'stock' => '20',
            'cost' => '80.99',
            'discontinued' => 'yes',
        ]));

        $this->assertNotNull($product);
        $this->assertNotNull($product->getDiscontinuedDate());
    }

    public function testImportTestModeEnabled()
    {
        $model = new ImportProduct(true);

        $product = $model->exec(new ProductCsv([
            'code' => 'P00001',
            'name' => 'Product Name',
            'description' => 'Product Description',
            'stock' => '20',
            'cost' => '80.99',
            'discontinued' => '',
        ]));

        $this->assertNotNull($product);
        $this->assertNull($product->getId());
    }

    public function testImportTestModeDisabled()
    {
        $model = new ImportProduct(false);

        $product = $model->exec(new ProductCsv([
            'code' => 'P00001',
            'name' => 'Product Name',
            'description' => 'Product Description',
            'stock' => '20',
            'cost' => '80.99',
            'discontinued' => '',
        ]));

        $this->assertNotNull($product);
        $this->assertNotNull($product->getId());
    }

    public function testImportAgain()
    {
        $model = Yii::createObject(ImportProduct::class);

        $product = new ProductCsv([
            'code' => 'P00001',
            'name' => 'Product Name',
            'description' => 'Product Description',
            'stock' => '20',
            'cost' => '80.99',
            'discontinued' => 'yes',
        ]);

        $model->exec($product);

        $this->expectException(DomainException::class);
        $this->expectExceptionCode(ImportProduct::EXCEPTION_CODE_PRODUCT_ALREADY_EXIST);

        $model->exec($product);

    }

    public static function productsProvider(): array
    {
        return [
            'success' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => '20',
                    'cost' => '80.99',
                    'discontinued' => '',
                ],
                true,
                'exceptionExpected' => null,
            ],
            'success discontinued product' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => '20',
                    'cost' => '80.99',
                    'discontinued' => 'yes',
                ],
                true,
                'exceptionExpected' => null,
            ],
            'empty fields' => [
                [
                    'code' => '',
                    'name' => '',
                    'description' => '',
                    'stock' => '',
                    'cost' => '',
                    'discontinued' => '',
                ],
                false,
                'exceptionExpected' => 'app\exceptions\InvalidCsvProductException',
            ],
            'invalid stock' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => 'invalid',
                    'cost' => '80.99',
                    'discontinued' => '',
                ],
                true,
                'exceptionExpected' => 'app\exceptions\InvalidCsvProductException',
            ],
            'invalid cost' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => '20',
                    'cost' => 'invalid',
                    'discontinued' => '',
                ],
                true,
                'exceptionExpected' => 'app\exceptions\InvalidCsvProductException',
            ],
            'invalid discontinued' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => '20',
                    'cost' => '80.99',
                    'discontinued' => 'invalid',
                ],
                true,
                'exceptionExpected' => 'app\exceptions\InvalidCsvProductException',
            ],
            'skipped with low cost (5) and stock (10)' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => '9',
                    'cost' => '4.99',
                    'discontinued' => '',
                ],
                false,
                'exceptionExpected' => null,
            ],
            'skipped big cost (1000)' => [
                [
                    'code' => 'P00001',
                    'name' => 'Product Name',
                    'description' => 'Product Description',
                    'stock' => '20',
                    'cost' => '1000.01',
                    'discontinued' => '',
                ],
                false,
                'exceptionExpected' => null,
            ],
        ];
    }
}