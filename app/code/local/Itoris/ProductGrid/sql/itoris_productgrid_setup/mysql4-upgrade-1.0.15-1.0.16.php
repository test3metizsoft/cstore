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

$data = $this->getConnection()->fetchAll("select distinct store_id, admin_id from {$this->getTable('itoris_productgrid_gridconfig')}");

$insert = array();

foreach ($data as $row) {
	$insert[] = "(" . $this->getConnection()->quote('show_action_column') . ", 1, 0, null, " . intval($row['store_id']) . ", " . intval($row['admin_id']) . ")";
}

if (!empty($insert)) {
	$newSettingsStr = implode(',', $insert);
	$this->getConnection()->query("insert into {$this->getTable('itoris_productgrid_gridconfig')} (`code`, `value`, `is_attribute`, `column_order`, `store_id`, `admin_id`) values {$newSettingsStr}");
}
?>