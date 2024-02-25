<?php

namespace app\cases;

use app\dto\ProductCsv;
use app\entities\Product;
use app\exceptions\InvalidCsvProductException;
use app\services\CurrencyServiceInterface;
use DateTimeZone;
use Decimal\Decimal;
use DomainException;
use Exception;
use InvalidArgumentException;

class ImportProduct
{
    const EXCEPTION_CODE_PRODUCT_ALREADY_EXIST = 1;

    private CurrencyServiceInterface $currencyService;

    /**
     * whether Test mode enabled
     */
    private bool $isTest = false;

    public function __construct(
        CurrencyServiceInterface $currencyService
    )
    {
        $this->currencyService = $currencyService;
    }

    public function changeTestMode(bool $value): self
    {
        $self = clone $this;
        $self->isTest = $value;
        return $self;
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
     * @return Decimal 5 USD in GBP
     * @throws Exception
     */
    private function get5UsdInGbp(): Decimal
    {
        static $_5UsdInGbp;
        if (!$_5UsdInGbp) {
            $_5UsdInGbp = $this->currencyService->convert('USD', 'GBP', new Decimal(5));
        }
        return $_5UsdInGbp;
    }

    /**
     * @return Decimal 1000 USD in GBP
     * @throws Exception
     */
    private function get1000UsdInGbp(): Decimal
    {
        static $_1000UsdInGbp;
        if (!$_1000UsdInGbp) {
            $_1000UsdInGbp = $this->currencyService->convert('USD', 'GBP', new Decimal(1000));
        }
        return $_1000UsdInGbp;
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
     * @throws Exception
     */
    private function needImportProduct(ProductCsv $product): bool
    {
        if (new Decimal($product->cost) < $this->get5UsdInGbp() && $product->stock < 10) {
            return false;
        }

        if (new Decimal($product->cost) > $this->get1000UsdInGbp()) {
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