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
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class Itoris_ProductGrid_Block_Admin_Catalog_Product_Grid_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
		$value = $this->_getValue($row);
		/** @var $image Mage_Catalog_Helper_Image */
		$image = Mage::helper('catalog/image');
		try {
			$image->init($row, 'thumbnail', $value);
		} catch (Exception $e) {
			// init image obj anyway
		}
		$image->resize(100, 75);
		$url = str_replace('adminhtml', 'frontend', $image->__toString());
		return '<img src="' . $url . '" />';
	}
}
?>