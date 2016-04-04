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

class Itoris_ProductGrid_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $alias = 'product_grid';
	private $isEnabledFlag = null;

	public function isAdminRegistered() {
		try {
			return Itoris_Installer_Client::isAdminRegistered($this->getAlias());
		} catch(Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return false;
		}
	}

	public function isRegisteredAutonomous($website = null) {
		return Itoris_Installer_Client::isRegisteredAutonomous($this->getAlias(), $website);
	}

	public function registerCurrentStoreHost($sn) {
		return Itoris_Installer_Client::registerCurrentStoreHost($this->getAlias(), $sn);
	}

	public function isRegistered($website) {
		return Itoris_Installer_Client::isRegistered($this->getAlias(), $website);
	}

	public function getAlias() {
		return $this->alias;
	}

	/**
	 * Get store id by parameter from the request
	 *
	 * @return int
	 */
	public function getStoreId() {
		if (Mage::app()->getRequest()->getParam('store')) {
			return Mage::app()->getStore(Mage::app()->getRequest()->getParam('store'))->getId();
		}
		return 0;
	}

	/**
	 * Get website id by parameter from the request
	 *
	 * @return int
	 */
	public function getWebsiteId() {
		if (Mage::app()->getRequest()->getParam('website')) {
			return Mage::app()->getWebsite(Mage::app()->getRequest()->getParam('website'))->getId();
		}
		return 0;
	}

	/**
	 * Get settings
	 *
	 * @return Itoris_PageContentSlider_Model_Settings
	 */
	public function getSettings($backend = false, $withoutProductId = true) {
		/** @var $settingsModel Itoris_ProductGrid_Model_Settings */
		$settingsModel = Mage::getSingleton('itoris_productgrid/settings');
		$productId = 0;
		if (!$withoutProductId && ($product = Mage::registry('current_product')) && $product instanceof Mage_Catalog_Model_Product) {
			$productId = $product->getId();
		}
		if ($backend || !Mage::app()->getWebsite()->getId()) {
			$settingsModel->load($this->getWebsiteId(), $this->getStoreId(), $productId);
		} else {
			$settingsModel->load(Mage::app()->getWebsite()->getId(), Mage::app()->getStore()->getId(), $productId);
		}

		return $settingsModel;
	}

	/**
	 * Get current date for current location
	 *
	 * @return Zend_Date
	 */
	public function getCurrentDate() {
		return Mage::app()->getLocale()->date();
	}

	/**
	 * Fix time if timezone is not default
	 *
	 * @param $dateOrigValue
	 * @return Zend_Date
	 */
	public function getDate($dateOrigValue, $valueIsSeconds = false) {
		$dateOrig = $valueIsSeconds ? new Zend_Date($dateOrigValue) : new Zend_Date($dateOrigValue, Zend_Date::ISO_8601);
		$dateWithTimezone = $valueIsSeconds ? new Zend_Date($dateOrig) : new Zend_Date($dateOrig, Zend_Date::ISO_8601);
		$currentTimezone = $this->getCurrentDate()->getTimezone();
		if ($dateWithTimezone->getTimezone() != $currentTimezone) {
			$dateWithTimezone->setTimezone($this->getCurrentDate()->getTimezone());
			$dateWithTimezone->setYear($dateOrig->getYear());
			$dateWithTimezone->setMonth($dateOrig->getMonth());
			$dateWithTimezone->setDay($dateOrig->getDay());
			$dateWithTimezone->setHour($dateOrig->getHour());
		}

		return $dateWithTimezone;
	}

	public function isEnabled() {
		if (is_null($this->isEnabledFlag)) {
			$storeId = (int) Mage::app()->getRequest()->getParam('store', 0);
			$store = Mage::app()->getStore($storeId);
			$this->isEnabledFlag = $this->getSettings(true)->getEnabled() && $this->isRegisteredAutonomous($store->getWebsite());
		}
		return $this->isEnabledFlag;
	}
}
 
?>