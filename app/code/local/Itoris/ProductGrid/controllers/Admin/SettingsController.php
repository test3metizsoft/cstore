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

class Itoris_ProductGrid_Admin_SettingsController extends Itoris_ProductGrid_Controller_Admin_Controller {

	public function indexAction() {
		$this->_getSession()->setBeforeUrl(Mage::helper('core/url')->getCurrentUrl());
		$websiteCode = $this->getRequest()->getParam('website');
		if (!empty($websiteCode)) {
			$website = Mage::app()->getWebsite($websiteCode);
			if (!Mage::helper('itoris_productgrid')->isRegistered($website)) {
				$error = '<b style="color:red">'
					. $this->__('The extension is not registered for the website selected. Please register it with an additional S/N.')
					. '</b>';
				Mage::getSingleton('adminhtml/session')->addError($error);
			}
		}

		$this->loadLayout();
		$this->renderLayout();
	}

	public function saveAction() {
		$websiteId = $this->getDataHelper()->getWebsiteId();
		$storeId = $this->getDataHelper()->getStoreId();
		if ($storeId) {
			$scope = 'store';
			$scopeId = (int)$storeId;
		} elseif ($websiteId) {
			$scope = 'website';
			$scopeId = $websiteId;
		} else {
			$scope = 'default';
			$scopeId = 0;
		}
		$settings = $this->getRequest()->getPost('settings', array());

		/** @var $model Itoris_ProductGrid_Model_Settings */
		$model = Mage::getModel('itoris_productgrid/settings');

		try{
			$model->save($settings, $scope, $scopeId);

			$this->_getSession()->addSuccess($this->__('Settings have been saved'));
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Settings have not been saved'));
		}

		$this->_redirectReferer($this->_getSession()->getBeforeUrl());
	}

	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('admin/system/itoris/productgrid');
	}
}
?>