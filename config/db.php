<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host=db;dbname={$_ENV['MYSQL_DATABASE']}",
    'username' => 'root',
    'password' => 'example',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
