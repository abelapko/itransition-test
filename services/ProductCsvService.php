<?php

namespace app\services;

use Iterator;
use League\Csv\Reader;

class ProductCsvService implements ProductCsvServiceInterface
{
    /**
     * @inheritDoc
     */
    public function readFromCsv(string $path): Iterator
    {
        $csv = Reader::createFromPath($path);
        // use csv headers as array keys for records
        $csv->setHeaderOffset(0);

        return $csv->getRecords();
    }

}