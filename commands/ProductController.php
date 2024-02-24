<?php

namespace app\commands;

use app\cases\ImportProduct;
use app\listeners\ImportResultCollector;
use app\services\ProductCsvService;
use app\cases\ImportProducts;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;

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
        $reporter = Yii::createObject(ImportResultCollector::class);

        $listeners = (new ListenerCollection())
            ->add([$reporter, 'onSuccess'])
            ->add([$reporter, 'onSkipped'])
            ->add([$reporter, 'onFailed']);
        $dispatcher = new Dispatcher(new Provider($listeners));

        $importer = new ImportProducts(
            new ProductCsvService(),
            $dispatcher,
            new ImportProduct($this->test)
        );

        $importer->exec($path);

        $reporter->logReport();

        return $reporter->hasErrors() ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

}