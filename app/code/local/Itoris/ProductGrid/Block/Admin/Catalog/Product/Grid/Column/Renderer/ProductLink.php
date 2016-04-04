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

class Itoris_ProductGrid_Block_Admin_Catalog_Product_Grid_Column_Renderer_ProductLink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


	public function render(Varien_Object $row) {
		$allowedVisibility = array(2,4);
		if ($row->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED && in_array($row->getVisibility(), $allowedVisibility)) {
			$storeId = $this->getRequest()->getParam('store');
			if (!$storeId) {
				$storeId = Mage::app()->getDefaultStoreView()->getId();
			}
			return '<a href="' . Mage::getSingleton('catalog/product_url')->getUrl($row, array('_store' => $storeId)) . '" class="preview_product_link" target="_blank"></a>';
		}
		return '';
	}
}

?>