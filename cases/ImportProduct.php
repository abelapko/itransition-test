<?php

namespace app\cases;

use app\dto\ProductCsv;
use app\entities\Product;
use app\exceptions\InvalidCsvProductException;
use DateTimeZone;
use Decimal\Decimal;
use DomainException;
use Exception;
use InvalidArgumentException;

class ImportProduct
{
    const EXCEPTION_CODE_PRODUCT_ALREADY_EXIST = 1;

    /**
     * whether Test mode enabled
     */
    private bool $isTest;

    public function __construct(
        bool $isTest = false
    )
    {
        $this->isTest = $isTest;
    }

    /**
     * @throws InvalidCsvProductException
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws Exception
     */
    public function exec(ProductCsv $productCsv): ?Product
    {
        self::ensureValidProduct($productCsv);

        if (!self::needImportProduct($productCsv)) {
            return null;
        }
        // convert DTO to entity
        $product = self::createProduct($productCsv);

        self::ensureCanBeImportToDb($product);

        // check on Test mode
        if (!$this->isTest) {
            return $product->save() ? $product : null;
        }

        return $product;
    }

    /**
     * Validate CSV record
     * @throws InvalidCsvProductException
     */
    private static function ensureValidProduct(ProductCsv $product): void
    {
        if (!$product->validate()) {
            throw InvalidCsvProductException::fromInvalidCsvRecord($product);
        }
    }

    /**
     * @return bool whether product from csv complies with import rules
     */
    private static function needImportProduct(ProductCsv $product): bool
    {
        if (new Decimal($product->cost) < new Decimal(5) && $product->stock < 10) {
            return false;
        }

        if (new Decimal($product->cost) > new Decimal(1000)) {
            return false;
        }

        return true;
    }

    private static function createProduct(ProductCsv $productCsv): Product
    {
        $product = new Product();

        $product->name = $productCsv->name;
        $product->description = $productCsv->description;
        $product->code = $productCsv->code;
        $product->discontinuedDate = $productCsv->isDiscontinued()
            ? date_create('now', new DateTimeZone('UTC'))
            : null;
        $product->stockLevel = $productCsv->stock;
        $product->price = new Decimal($productCsv->cost);

        return $product;
    }

    /**
     * Pre insert validation
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    private static function ensureCanBeImportToDb(Product $product): void
    {
        // validate entity of DB
        if (!$product->validate()) {
            $error = current($product->getFirstErrors());
            throw new InvalidArgumentException("Validation error: $error.");
        }
        // check on uniq code
        if ($product::hasByCode($product->code)) {
            throw new DomainException("Product with code '$product->code' already exist in Base.", self::EXCEPTION_CODE_PRODUCT_ALREADY_EXIST);
        }
    }
}