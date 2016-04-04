<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_PRODUCTGRID
 * @copyright  Copyright (c) 2014 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */


$this->startSetup();

$this->run("

create table {$this->getTable('itoris_productgrid_admin')} (
	`gridadmin_id` int unsigned not null auto_increment primary key,
	`admin_id` int(10) unsigned not null,
	`store_id`smallint(5) unsigned null,
	`grid_filter` text null,
	`grid_sort_column` varchar(50) null,
	`grid_sort_dir` varchar(4) null,
	constraint FK_ITORIS_PRODUCTGRID_ADMIN_ID foreign key (`admin_id`) references {$this->getTable('admin_user')} (`user_id`) on delete cascade on update cascade,
	constraint FK_ITORIS_PRODUCTGRID_ADMIN_STORE_ID foreign key (`store_id`) references {$this->getTable('core_store')} (`store_id`) on delete cascade on update cascade
) engine = InnoDB default charset = utf8;

");

$this->endSetup();
?>