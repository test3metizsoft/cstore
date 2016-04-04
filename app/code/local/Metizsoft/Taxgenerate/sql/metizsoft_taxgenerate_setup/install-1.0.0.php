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
    ->newTable($this->getTable('metizsoft_taxgenerate/statetax'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Statetax ID'
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
        'state',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'State'
    )
    ->addColumn(
        'state_tax',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'State Tax'
    )
    ->addColumn(
        'county',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'County'
    )
    ->addColumn(
        'county_tax',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'County Tax'
    )
    ->addColumn(
        'city',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'City'
    )
    ->addColumn(
        'zipcode',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'City Zipcode'
    )
    ->addColumn(
        'city_tax',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'City Tax'
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
    ->setComment('Statetax Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/countylist'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Countylist ID'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Name'
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
        'Countylist Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Countylist Creation Time'
    ) 
    ->setComment('Countylist Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/citylist'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Citylist ID'
    )
    ->addColumn(
        'county',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
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
        'zipcodes',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(
            'nullable'  => false,
        ),
        'Zipcodes'
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
        'Citylist Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Citylist Creation Time'
    ) 
    ->setComment('Citylist Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/statetax_store'))
    ->addColumn(
        'statetax_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'Statetax ID'
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
            'metizsoft_taxgenerate/statetax_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/statetax_store',
            'statetax_id',
            'metizsoft_taxgenerate/statetax',
            'entity_id'
        ),
        'statetax_id',
        $this->getTable('metizsoft_taxgenerate/statetax'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/statetax_store',
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
    ->setComment('Statetaxs To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/countylist_store'))
    ->addColumn(
        'countylist_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'Countylist ID'
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
            'metizsoft_taxgenerate/countylist_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/countylist_store',
            'countylist_id',
            'metizsoft_taxgenerate/countylist',
            'entity_id'
        ),
        'countylist_id',
        $this->getTable('metizsoft_taxgenerate/countylist'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/countylist_store',
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
    ->setComment('Countylists To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('metizsoft_taxgenerate/citylist_store'))
    ->addColumn(
        'citylist_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'Citylist ID'
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
            'metizsoft_taxgenerate/citylist_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/citylist_store',
            'citylist_id',
            'metizsoft_taxgenerate/citylist',
            'entity_id'
        ),
        'citylist_id',
        $this->getTable('metizsoft_taxgenerate/citylist'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'metizsoft_taxgenerate/citylist_store',
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
    ->setComment('Citylists To Store Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();
