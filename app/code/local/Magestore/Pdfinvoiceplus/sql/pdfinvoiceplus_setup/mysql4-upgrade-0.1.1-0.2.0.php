<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create pdfinvoiceplus table
 */
/* Change by Jack 02/12 */
$installer->run("
ALTER TABLE  {$this->getTable('pdfinvoiceplus_template')} 
    ADD COLUMN `localization` varchar(255) NOT NULL default 'default',
    ADD COLUMN `footer_height` int(11) NOT NULL default '60', 
    ADD COLUMN `order_html` mediumtext NOT NULL default '',
    ADD COLUMN `invoice_html` mediumtext NOT NULL default '',
    ADD COLUMN `creditmemo_html` mediumtext NOT NULL default '';
 ALTER TABLE {$this->getTable('pdfinvoiceplus_system_template')}
    ADD COLUMN `type_format` varchar(255) NOT NULL DEFAULT '',
    ADD COLUMN `sort_order` int(11) NOT NULL DEFAULT '0';
");
/*End Change */
$installer->endSetup();

