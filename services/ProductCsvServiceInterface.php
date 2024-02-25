<?php

namespace app\services;

use Exception;
use Iterator;

interface ProductCsvServiceInterface
{
    /**
     * @return Iterator<array>
     * @throws Exception
     */
    public function readFromCsv(string $path): Iterator;
}
