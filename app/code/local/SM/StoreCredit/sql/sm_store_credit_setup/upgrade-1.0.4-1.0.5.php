<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote_payment'),'sm_store_credit_pay_due', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 30,
        'comment'   => 'sm_store_credit_pay_due'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_payment'),'sm_store_credit_pay_due', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 30,
        'comment'   => 'sm_store_credit_pay_due'
    ));

$installer->endSetup();