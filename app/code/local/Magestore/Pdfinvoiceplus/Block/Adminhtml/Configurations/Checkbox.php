<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Pdfinvoiceplus Configurations Checkbox Model
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Configurations_Checkbox extends Mage_Adminhtml_Block_System_Config_Form_Field {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('pdfinvoiceplus/configurations/checkbox.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }

    protected function _getConfig($path) {
        $storeCode = Mage::app()->getRequest()->getParam('store');
        $websiteCode = Mage::app()->getRequest()->getParam('website');
        if ($storeCode)
            return Mage::app()->getStore($storeCode)->getConfig($path);
        if ($websiteCode)
            return Mage::app()->getWebsite($websiteCode)->getConfig($path);
        return (string) Mage::getConfig()->getNode('default/' . $path);
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 1, 'checked' => true, 'label' => Mage::helper('adminhtml')->__('Option 1')),
            array('value' => 2, 'checked' => '', 'label' => Mage::helper('adminhtml')->__('Option 2')),
        );
    }

}
