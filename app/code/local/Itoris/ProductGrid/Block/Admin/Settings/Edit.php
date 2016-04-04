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

class Itoris_ProductGrid_Block_Admin_Settings_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
		parent::__construct();
		$this->_headerText = $this->__('Configuration');
		$this->_blockGroup = 'itoris_productgrid';
		$this->_controller = 'admin_settings';
		$this->removeButton('back');
		$this->removeButton('reset');
		$this->removeButton('delete');
	}

	/**
	 * Get save url for form
	 *
	 * @return string
	 */
	public function getSaveUrl() {
		$website = $this->getRequest()->getParam('website');
		$params = array();
		if ($website) {
			$params['website'] = $website;
		}
		$store = $this->getRequest()->getParam('store');
		if ($store) {
			$params['store'] = $store;
		}
		return Mage::helper('adminhtml')->getUrl('*/*/save', $params);
	}

}
?>