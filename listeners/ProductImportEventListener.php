<?php

namespace app\listeners;

use app\events\ProductFailedImported;
use app\events\ProductSkippedImport;
use app\events\ProductSuccessImported;

interface ProductImportEventListener
{
    public function onSuccess(ProductSuccessImported $event): void;

    public function onFailed(ProductFailedImported $event): void;

    public function onSkipped(ProductSkippedImport $event): void;
}
