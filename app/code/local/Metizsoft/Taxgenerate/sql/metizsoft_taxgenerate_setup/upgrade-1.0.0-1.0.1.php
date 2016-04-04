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

$table = $this->getTable('metizsoft_taxgenerate/citylist');
$this->run('ALTER TABLE  '.$table.' CHANGE  `county`  `county_id` INT( 11 ) NOT NULL COMMENT  "County"');
$this->getConnection()->addColumn(
        $this->getTable('metizsoft_taxgenerate/statetax'),
        'taxtype',      //column name
        'varchar(11) NOT NULL AFTER `state`'  //datatype definition
        );
$this->run('ALTER TABLE '.$this->getTable('metizsoft_taxgenerate/statetax').' DROP `county`');
$this->run('ALTER TABLE '.$this->getTable('metizsoft_taxgenerate/statetax').' DROP `county_tax`');
$this->run('ALTER TABLE '.$this->getTable('metizsoft_taxgenerate/statetax').' DROP `city`');
$this->run('ALTER TABLE '.$this->getTable('metizsoft_taxgenerate/statetax').' DROP `zipcode`');
$this->run('ALTER TABLE '.$this->getTable('metizsoft_taxgenerate/statetax').' DROP `city_tax`');

$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/citytax'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Citytax ID'
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
        'county_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'County id'
    )
    ->addColumn(
        'city_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'City id'
    )
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Category'
    )
    ->addColumn(
        'taxtype',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Tax type'
    )
    ->addColumn(
        'city_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',
        array(
            'nullable'  => false,
        ),
        'City tax'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'Enabled'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Statetax Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Statetax Creation Time'
    ) 
    ->setComment('Citytax Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/citytax_store'))
    ->addColumn(
        'citytax_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'Citytax ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'metizsoft_taxgenerate/citytax_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/citytax_store',
            'citytax_id',
            'metizsoft_taxgenerate/citytax',
            'entity_id'
        ),
        'citytax_id',
        $this->getTable('metizsoft_taxgenerate/citytax'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/citytax_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Citytaxs To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/countytax'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Citytax ID'
    )
    ->addColumn(
        'county_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'County id'
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
        'category_id',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Category'
    )
    ->addColumn(
        'taxtype',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Tax type'
    )
    ->addColumn(
        'county_tax',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',
        array(
            'nullable'  => false,
        ),
        'County tax'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'Enabled'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Statetax Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Statetax Creation Time'
    ) 
    ->setComment('Countytax Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/countytax_store'))
    ->addColumn(
        'countylist_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'CountyList ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'metizsoft_taxgenerate/countytax_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/countytax_store',
            'citytax_id',
            'metizsoft_taxgenerate/countytax',
            'entity_id'
        ),
        'countylist_id',
        $this->getTable('metizsoft_taxgenerate/countytax'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/countytax_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Countytaxs To Store Linkage Table');
$this->getConnection()->createTable($table);


$this->endSetup();