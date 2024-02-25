<?php

use yii\db\Migration;

class m240222_155840_add_columns_stock_level_and_price_to_table_product_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tblProductData', 'intStockLevel', $this->integer()->unsigned()->notNull());
        $this->addColumn(
            'tblProductData',
            'decPrice',
            $this->decimal(10, 2)->unsigned()->notNull()->comment('in GBP currency')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tblProductData', 'intStockLevel');
        $this->dropColumn('tblProductData', 'decPrice');
    }
}
