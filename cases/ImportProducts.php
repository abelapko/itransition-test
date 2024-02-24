<?php

namespace app\cases;

use app\dto\ProductCsv;
use app\entities\Product;
use app\events\ProductFailedImported;
use app\events\ProductSkippedImport;
use app\events\ProductSuccessImported;
use app\exceptions\InvalidCsvProductException;
use app\services\ProductCsvServiceInterface;
use DomainException;
use Exception;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;

class ImportProducts
{
    private ProductCsvServiceInterface $productCsvService;

    private EventDispatcherInterface $dispatcher;

    private ImportProduct $importer;

    public function __construct(
        ProductCsvServiceInterface $productCsvService,
        EventDispatcherInterface $dispatcher,
        ImportProduct $importer
    )
    {
        $this->productCsvService = $productCsvService;
        $this->dispatcher = $dispatcher;
        $this->importer = $importer;
    }

    /**
     * @return Product[]
     * @throws Exception
     */
    public function exec(string $path): array
    {
        // read from csv file
        $records = $this->productCsvService->readFromCsv($path);

        /** @var Product[] $imported */
        $imported = [];
        foreach ($records as $record) {
            // convert to DTO
            $productCsv = ProductCsv::fromCsvRecord($record);

            try {
                // import
                $product = $this->importer->exec($productCsv);
            } catch (InvalidCsvProductException|InvalidArgumentException|DomainException|Exception $e) {
                // notify about failed record
                $this->dispatcher->dispatch(new ProductFailedImported($productCsv, $e));
                // go to next record
                continue;
            }

            if ($product) {
                $imported[] = $product;
                // notify about success import
                $this->dispatcher->dispatch(new ProductSuccessImported($productCsv));
            } else {
                // notify about skipped import
                $this->dispatcher->dispatch(new ProductSkippedImport($productCsv));
            }

        }

        return $imported;
    }


}