<?php

use yii\db\Migration;

class m240221_153040_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // sql code provided from task
        $sql = <<< SQL
            CREATE TABLE tblProductData (
              intProductDataId int(10) unsigned NOT NULL AUTO_INCREMENT,
              strProductName varchar(50) NOT NULL,
              strProductDesc varchar(255) NOT NULL,
              strProductCode varchar(10) NOT NULL,
              dtmAdded datetime DEFAULT NULL,
              dtmDiscontinued datetime DEFAULT NULL,
              stmTimestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (intProductDataId),
              UNIQUE KEY (strProductCode)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores product data';
        SQL;
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tblProductData');
    }
}
