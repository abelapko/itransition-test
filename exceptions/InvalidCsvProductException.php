<?php

namespace app\exceptions;

use app\dto\ProductCsv;
use Exception;

class InvalidCsvProductException extends Exception
{
    private ProductCsv $product;

    public static function fromInvalidCsvRecord(ProductCsv $product): self
    {
        $attr = key($product->getFirstErrors());
        $error = current($product->getFirstErrors());
        $strValue = json_encode($product->$attr);

        $self = new self("Invalid CSV record: $error But passed value: $strValue");
        $self->product = $product;

        return $self;
    }

    public function getProduct(): ProductCsv
    {
        return $this->product;
    }

    public function getErrorField(): string
    {
        return key($this->product->getFirstErrors());
    }
}