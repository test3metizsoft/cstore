<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote'),'sm_pay_due_amount', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 10,
        'comment'   => 'sm_pay_due_amount'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),'sm_pay_due_amount', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 10,
        'comment'   => 'sm_pay_due_amount'
    ));

$installer->endSetup();