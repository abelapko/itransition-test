<?php

namespace app\listeners;

use app\events\ProductFailedImported;
use app\events\ProductSkippedImport;
use app\events\ProductSuccessImported;
use Psr\Log\LoggerInterface;
use Codedungeon\PHPCliColors\Color as C;

class ImportResultCollector implements ProductImportEventListener
{
    private LoggerInterface $logger;

    private int $counter = 0;
    /**
     * @var array<int,ProductSuccessImported>
     */
    private array $success = [];
    /**
     * @var array<int,ProductFailedImported>
     */
    private array $failed = [];
    /**
     * @var array<int,ProductSkippedImport>
     */
    private array $skipped = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onSuccess(ProductSuccessImported $event): void
    {
        $this->counter++;
        $this->success[$this->counter] = $event;
    }

    public function onFailed(ProductFailedImported $event): void
    {
        $this->counter++;
        $this->failed[$this->counter] = $event;
    }

    public function onSkipped(ProductSkippedImport $event): void
    {
        $this->counter++;
        $this->skipped[$this->counter] = $event;
    }

    private function getAllEvents(): array
    {
        return array_merge($this->success, $this->failed, $this->skipped);
    }

    public function logReport()
    {
        $this->logger->info(
            PHP_EOL .
            C::BG_WHITE     . "Processed:\t"  . count($this->getAllEvents()) . C::RESET . PHP_EOL .
            C::BG_GREEN     . "Successful:\t" . count($this->success) . C::RESET . PHP_EOL .
            C::BG_DARK_GRAY . "Skipped:\t"    . count($this->skipped) . C::RESET . PHP_EOL .
            C::BG_RED       . "Failed:\t\t"     . count($this->failed)  . C::RESET . PHP_EOL .
                ($this->failed ? $this->getFailedItemsStr() : '')
        );
    }

    public function hasErrors(): bool
    {
        return !empty($this->failed);
    }

    private function getFailedItemsStr(): string
    {
        $result = '';

        foreach ($this->failed as $number => $event) {
            $product = $event->getProductCsv();
            $exception = $event->getException();
            $strCode = json_encode($product->code);
            $result .= " - \t#$number \tCode: $strCode \tError: " . C::BG_RED . $exception->getMessage() . C::RESET . PHP_EOL;
        }

        return $result;
    }
}