<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote'),'sm_current_balance', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 10,
        'comment'   => 'sm_current_balance'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),'sm_current_balance', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'length'    => 10,
        'comment'   => 'sm_current_balance'
    ));

$installer->endSetup();