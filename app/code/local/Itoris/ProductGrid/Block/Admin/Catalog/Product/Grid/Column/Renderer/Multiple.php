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

class Itoris_ProductGrid_Block_Admin_Catalog_Product_Grid_Column_Renderer_Multiple extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options {

	public function render(Varien_Object $row) {
		$options = $this->getColumn()->getOptions();
		$showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
		if (!empty($options) && is_array($options)) {
			$value = $row->getData($this->getColumn()->getIndex());
			$value = explode(',', $value);
			if (is_array($value)) {
				$res = array();
				foreach ($value as $item) {
					if (isset($options[$item])) {
						$res[] = $this->escapeHtml($options[$item]);
					}
					elseif ($showMissingOptionValues) {
						$res[] = $this->escapeHtml($item);
					}
				}
				return implode(', ', $res);
			} elseif (isset($options[$value])) {
				return $this->escapeHtml($options[$value]);
			} elseif (in_array($value, $options)) {
				return $this->escapeHtml($value);
			}
		}
	}
}
?>