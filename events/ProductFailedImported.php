<?php

namespace app\events;

use app\dto\ProductCsv;
use Exception;

class ProductFailedImported extends ProductImportEvent
{
    private Exception $exception;

    public function __construct(ProductCsv $product, Exception $exception)
    {
        $this->exception = $exception;
        parent::__construct($product);
    }

    public function getException(): Exception
    {
        return $this->exception;
    }
}