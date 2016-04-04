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
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('pdfinvoiceplus_system_template')};
CREATE TABLE {$this->getTable('pdfinvoiceplus_system_template')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `template_name` varchar(255) NOT NULL default '',
  `template_code` varchar(255) NOT NULL default '',
  `image` varchar(255) default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('pdfinvoiceplus_template')};
CREATE TABLE {$this->getTable('pdfinvoiceplus_template')} (
  `template_id` int(11) unsigned NOT NULL auto_increment,
  `template_name` varchar(255) NOT NULL default '',
  `stores` text default '',
  `system_template_id` int(11) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `is_active` tinyint(1) NOT NULL default '1',
  `css` text default '',
  `order_filename` varchar(255) default '',
  `invoice_filename` varchar(255) default '',
  `creditmemo_filename` varchar(255) default '',
  `vat_number` varchar(255) NOT NULL default '',
  `format` varchar(255) NOT NULL default '',
  `created_at` datetime NULL,
  `footer` text default '',
  `note` text default '',
  `color` varchar(255) default '',
  `company_logo` varchar(255) default '',
  `company_address` text default '',
  `company_fax` varchar(255) default '',
  `company_telephone` varchar(255) default '',
  `company_name` varchar(255) default '',
  `company_email` varchar(255) default '',
  `business_id` varchar(255) default '',
  `orientation` tinyint(1) NOT NULL default '0',
  `terms_conditions` text default '',
  `barcode` tinyint(1) NOT NULL default '1',
  `barcode_type` varchar(255) default '',
  `display_images` tinyint(1) NOT NULL default '1',
  `vat_office` text default '',
  `barcode_order` varchar(255) default '',
  `barcode_invoice`varchar(255) default '',
  `barcode_creditmemo` varchar(255) default '',
  PRIMARY KEY (`template_id`),
  INDEX (`system_template_id`),
  FOREIGN KEY (`system_template_id`) REFERENCES {$this->getTable('pdfinvoiceplus_system_template')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



");

$installer->endSetup();

