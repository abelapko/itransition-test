<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class DevController extends Controller
{
    public function actionDbTest(): int
    {
        $db = Yii::$app->db;
        // log db settings
        $this->stdout('creds: ' . json_encode(['username' => $db->username, 'password' => $db->password]) . PHP_EOL);
        $this->stdout('db dns: ' . $db->dsn . PHP_EOL);
        // try exec test query
        $databases = $db->createCommand('SELECT DATABASE() FROM DUAL;')
            ->queryAll();
        // print result
        $this->stdout('databases: ' . json_encode($databases) . PHP_EOL);

        $this->stdout('Connected success!' . PHP_EOL);

        return ExitCode::OK;
    }
}
