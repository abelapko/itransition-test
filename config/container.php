<?php

use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;

return [
    'definitions' => [
        'app\services\ProductCsvServiceInterface' => 'app\services\ProductCsvService',
        'Psr\Log\LoggerInterface' => [
            'class' => 'alexeevdv\yii\PsrLoggerAdapter',
            'logger' => Yii::getLogger(),
        ],
        'Psr\EventDispatcher\EventDispatcherInterface' => function () {
            $listeners = new ListenerCollection();
            $provider = new Provider($listeners);
            return new Dispatcher($provider);
        },
    ],
];