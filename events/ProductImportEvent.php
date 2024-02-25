<?php

namespace app\events;

use app\dto\ProductCsv;

abstract class ProductImportEvent
{
    protected ProductCsv $product;

    public function __construct(ProductCsv $product)
    {
        $this->product = $product;
    }

    public function getProductCsv(): ProductCsv
    {
        return $this->product;
    }
}
