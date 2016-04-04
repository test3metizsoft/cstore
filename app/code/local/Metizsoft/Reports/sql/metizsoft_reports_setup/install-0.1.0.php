<?php
/**
 * Metizsoft_Taxgenerate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Metizsoft
 * @package        Metizsoft_Taxgenerate
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Taxgenerate module install script
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable('custom_reports')
    ->addColumn(
        'reports_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Reports id'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        array(
            'nullable'  => false,
        ),
        'Order id'
    )
    ->addColumn(
        'order_number',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Order number'
    )
    ->addColumn(
        'orderitemid',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Order item id'
    )
    ->addColumn(
        'created',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(),
        'Order Created DateTime'
    )
    ->addColumn(
        'state',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'State'
    )
    ->addColumn(
        'statecode',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Statecode'
    )
    ->addColumn(
        'county',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'County'
    )
    ->addColumn(
        'city',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'City'
    )
    ->addColumn(
        'productname',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Product name'
    )
    ->addColumn(
        'order_qty',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Order qty'
    )
    ->addColumn(
        'order_refunded',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'order refunded qty'
    )
    ->addColumn(
        'statetax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',
        array(
            'nullable'  => false,
            'default'   => '0.0000',
        ),
        'State tax'
    )
    ->addColumn(
        'citytax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',
        array(
            'nullable'  => false,
            'default'   => '0.0000',
        ),
        'City tax'
    )
    ->addColumn(
        'countytax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',
        array(
            'nullable'  => false,
            'default'   => '0.0000',
        ),
        'County tax'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Product id'
    )
    ->addColumn(
        'cat_id',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Category id'
    )
    ->addColumn(
        'catname',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Category Name'
    )
    ->addColumn(
        'protaxunit',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Product tax unit'
    )
    ->setComment('Custom Tax report Table');
$this->getConnection()->createTable($table);
$this->endSetup();