<?php

namespace app\tests\unit\entities;

use app\entities\Product;

class ProductTest extends \Codeception\Test\Unit
{

    /**
     * @dataProvider productsProvider
     */
    public function testValidation(array $productData, bool $validExpected, ?string $attrErrExpected)
    {
        $product = new Product($productData);

        $isValid = $product->validate();
        $attr = key($product->getFirstErrors());

        $this->assertEquals($validExpected, $isValid);
        $this->assertEquals($attrErrExpected, $attr);
    }

    public static function productsProvider(): array
    {
        return [
            'success' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => true,
                'attrError' => null,
            ],
            'without name' => [
                [
                    'strProductName' => null,
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductName',
            ],
            'without description' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => null,
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductDesc',
            ],
            'without code' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => null,
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductCode',
            ],
            'without stock level' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => null,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'intStockLevel',
            ],
            'without price' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => null,
                ],
                'valid' => false,
                'attrError' => 'decPrice',
            ],
            'invalid name' => [
                [
                    'strProductName' => 1.1,
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductName',
            ],
            'long name' => [
                [
                    'strProductName' => str_repeat('a', 51),
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductName',
            ],
            'empty name' => [
                [
                    'strProductName' => '',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductName',
            ],
            'invalid description' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 1.1,
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductDesc',
            ],
            'long description' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => str_repeat('a', 256),
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductDesc',
            ],
            'empty description' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => '',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductDesc',
            ],
            'invalid code' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 1.1,
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductCode',
            ],
            'long code' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => str_repeat('a', 11),
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductCode',
            ],
            'empty code' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => '',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'strProductCode',
            ],
            'invalid stock level' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 1.1,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'intStockLevel',
            ],
            'empty stock level' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => '',
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'intStockLevel',
            ],
            'negative stock level' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => -1,
                    'decPrice' => '80.99',
                ],
                'valid' => false,
                'attrError' => 'intStockLevel',
            ],
            'invalid price' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => 'invalid',
                ],
                'valid' => false,
                'attrError' => 'decPrice',
            ],
            'empty price' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '',
                ],
                'valid' => false,
                'attrError' => 'decPrice',
            ],
            'negative price' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Product description',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '-80.99',
                ],
                'valid' => false,
                'attrError' => 'decPrice',
            ],
            'text with Russian charts' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => 'Описание продукта',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => true,
                'attrError' => null,
            ],
            'text with Chinese charts' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => '产品名称',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => true,
                'attrError' => null,
            ],
            'text with emoji' => [
                [
                    'strProductName' => 'Product name',
                    'strProductDesc' => '😉🤖😜💟🈶',
                    'strProductCode' => 'P0001',
                    'intStockLevel' => 20,
                    'decPrice' => '80.99',
                ],
                'valid' => true,
                'attrError' => null,
            ],
        ];
    }
}