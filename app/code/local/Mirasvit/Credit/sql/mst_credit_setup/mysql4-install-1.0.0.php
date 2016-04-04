<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Store Credit & Refund
 * @version   1.0.0
 * @build     307
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


// @codingStandardsIgnoreFile
$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_credit');
if ($version == '1.0.0') {
    return;
}

$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('credit/balance')}`;
       DROP TABLE IF EXISTS `{$this->getTable('credit/transaction')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('credit/balance')}` (
    `balance_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(10) unsigned,
    `amount` FLOAT,
    `is_subscribed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    KEY `fk_credit_balance_customer_id` (`customer_id`),
    CONSTRAINT `mst_a58b860585684c1663873d88a9ff6bc8` FOREIGN KEY (`customer_id`)
      REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`balance_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('credit/transaction')}` (
    `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
    `balance_id` INT(11) NOT NULL,
    `balance_amount` FLOAT,
    `balance_delta` FLOAT,
    `action` VARCHAR(255) NOT NULL DEFAULT '',
    `message` TEXT,
    `is_notified` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    KEY `fk_credit_transaction_balance_id` (`balance_id`),
    CONSTRAINT `mst_f154dafdca29619823857363b3274c5d` FOREIGN KEY (`balance_id`)
      REFERENCES `{$this->getTable('credit/balance')}` (`balance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

    
ALTER TABLE `{$this->getTable('sales/creditmemo')}` ADD COLUMN `base_credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/creditmemo')}` ADD COLUMN `credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/creditmemo')}` ADD COLUMN `base_credit_total_refunded` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/creditmemo')}` ADD COLUMN `credit_total_refunded` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/invoice')}` ADD COLUMN `base_credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/invoice')}` ADD COLUMN `credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `base_credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `base_credit_invoiced` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `credit_invoiced` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `base_credit_refunded` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `credit_refunded` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `base_credit_total_refunded` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/order')}` ADD COLUMN `credit_total_refunded` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/quote')}` ADD COLUMN `use_credit` TINYINT(1) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/quote')}` ADD COLUMN `base_credit_amount_used` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/quote')}` ADD COLUMN `credit_amount_used` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD COLUMN `base_credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
    
ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD COLUMN `credit_amount` DECIMAL(12, 4) NOT NULL DEFAULT 0;
    
    
";
$installer->run($sql);

$installer->endSetup();