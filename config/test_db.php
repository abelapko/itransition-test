<?php
$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = "mysql:host=db-test;dbname={$_ENV['MYSQL_DATABASE']}";

return $db;
