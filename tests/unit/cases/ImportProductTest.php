<?php

namespace app\tests\unit\cases;

use app\cases\ImportProduct;
use app\dto\ProductCsv;
use app\services\CurrencyService;
use app\services\CurrencyServiceInterface;
use Closure;
use Decimal\Decimal;
use DomainException;
use InvalidArgumentException;
use Yii;

class ImportProductTest extends \Codeception\Test\Unit
{
    const RATE_USD_TO_GBP_MOCKED = "0.79";

    /**
     * @dataProvider productsProvider
     */
    public function testImport(array $productData, bool $expectedImport, ?string $exceptionExpected)
    {
        $model = new ImportProduct($this->getCurrencyService());

        if ($exceptionExpected) {
            $this->expectException($exceptionExpected);
        }

        $isImported = !! $model->exec(new ProductCsv($productData));

        $this->assertEquals($expectedImport, $isImported);
    }

    public function testImportDiscontinuedProduct()
    {
        $model = new ImportProduct($this->getCurrencyService());

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
        $model = (new ImportProduct($this->getCurrencyService()))
            ->changeTestMode(true);

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
        $model = (new ImportProduct($this->getCurrencyService()))
            ->changeTestMode(false);

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
        $model = new ImportProduct($this->getCurrencyService());

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
                    'cost' => (string) (4.95 * self::RATE_USD_TO_GBP_MOCKED),
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

    private function getCurrencyService(): CurrencyServiceInterface
    {
        return $this->make(
            CurrencyService::class,
            ['convert' => Closure::fromCallable([self::class, 'convertMocked'])]
        );
    }

    private static function convertMocked(string $from, string $to, Decimal $value): Decimal
    {
        if ($from === 'USD' && $to === 'GBP') {
           return (new Decimal($value))->mul(self::RATE_USD_TO_GBP_MOCKED);
        }

        throw new InvalidArgumentException();
    }
}