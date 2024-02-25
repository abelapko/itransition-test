<?php

namespace app\commands;

use app\cases\ImportProduct;
use app\listeners\ImportResultCollector;
use app\cases\ImportProducts;
use app\services\ProductCsvServiceInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;
use Codedungeon\PHPCliColors\Color as C;

class ProductController extends Controller
{
    /**
     * @var bool enabled or disable Test mode
     */
    public bool $test = false;

    public function options($actionID): array
    {
        return ['test'];
    }

    public function optionAliases(): array
    {
        return ['t' => 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function getActionOptionsHelp($action): array
    {
        return [
            'test' => [
                'type' => 'boolean',
                'default' => false,
                'comment' => 'Whether run in Test mode. Usage: -t=1',
            ],
        ];
    }

    /**
     * @param string $path path to file for import
     * @throws Exception
     */
    public function actionImport(string $path = '/app/resources/stock.csv'): int
    {
        try {
            // reporter for print results to console
            $reporter = Yii::createObject(ImportResultCollector::class);
            // setup listeners and dispatcher
            $listeners = (new ListenerCollection())
                ->add([$reporter, 'onSuccess'])
                ->add([$reporter, 'onSkipped'])
                ->add([$reporter, 'onFailed']);
            $dispatcher = new Dispatcher(new Provider($listeners));
            // setup importer
            $importer = new ImportProducts(
                Yii::createObject(ProductCsvServiceInterface::class),
                $dispatcher,
                Yii::createObject(ImportProduct::class)
                    ->changeTestMode($this->test)
            );
            // run import
            $importer->exec($path);
            // print results
            $reporter->logReport();
            // correct exit
            return $reporter->hasErrors() ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
        } catch (Throwable $e) {
            Yii::createObject(LoggerInterface::class)
                ->error(C::BG_RED . $e->getMessage() . C::RESET);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
