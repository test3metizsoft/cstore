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


class Itoris_ProductGrid_Model_Mysql4_Admin extends Mage_Core_Model_Mysql4_Abstract {

	protected function _construct() {
		$this->_init('itoris_productgrid/admin', 'gridadmin_id');
	}

	protected function _getLoadSelect($field, $value, $object) {
		$select = parent::_getLoadSelect($field, $value, $object);
		$select->where('store_id=0 or store_id=?', $object->getCurrentStoreId())
			->order('store_id desc')
			->limit(1);

		return $select;
	}
}
?>