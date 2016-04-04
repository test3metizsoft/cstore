<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote_payment'),'sm_store_credit_pay_previous', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 2,
        'comment'   => 'sm_store_credit_pay_previous'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_payment'),'sm_store_credit_pay_previous', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 2,
        'comment'   => 'sm_store_credit_pay_previous'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote_payment'),'sm_store_credit_pay_previous_value', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 40,
        'comment'   => 'sm_store_credit_pay_previous_value'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_payment'),'sm_store_credit_pay_previous_value', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 40,
        'comment'   => 'sm_store_credit_pay_previous_value'
    ));

$installer->endSetup();