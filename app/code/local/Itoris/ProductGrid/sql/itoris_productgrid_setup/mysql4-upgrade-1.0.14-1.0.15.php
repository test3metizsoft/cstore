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

$this->run("
	alter table {$this->getTable('itoris_productgrid_gridconfig')} add `admin_id` int(10) unsigned null;
");

$data = $this->getConnection()->fetchAll("select * from {$this->getTable('itoris_productgrid_gridconfig')}");
$adminSettings = array();
$users = Mage::getModel('admin/user')->getCollection();
if (count($users)) {
	foreach ($users as $user) {
		foreach ($data as $row) {
			$adminSettings[] = "(" . $this->getConnection()->quote($row['code']) . ", " . $this->getConnection()->quote($row['value']) . ", " . intval($row['is_attribute'])
				. ", " . intval($row['column_order']) . ", " . intval($row['store_id']) . ", " . intval($user->getId()) . ")";
		}
	}
}
if (!empty($adminSettings)) {
	$newSettingsStr = implode(',', $adminSettings);
	$this->getConnection()->query("insert into {$this->getTable('itoris_productgrid_gridconfig')} (`code`, `value`, `is_attribute`, `column_order`, `store_id`, `admin_id`) values {$newSettingsStr}");
}
$this->getConnection()->query("delete from {$this->getTable('itoris_productgrid_gridconfig')} where admin_id is null");
?>