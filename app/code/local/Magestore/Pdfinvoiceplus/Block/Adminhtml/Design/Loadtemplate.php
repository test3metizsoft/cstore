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
 * Pdfinvoiceplus Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Design_Loadtemplate extends Mage_Core_Block_Template {

    protected $_locale;
    protected $_template_object;
    protected $_data_object;


    public function __construct() {
        $this->_locale = Mage::helper('pdfinvoiceplus/localization');
    }
    
    public function setLocale($code){
        if(is_object($this->_locale)){
            $this->_locale->setLocalization($code);
        }
        return $this;
    }
    
    public function setTemplateObject($model){
        $this->_template_object = $model;
        return $this;
    }
    
    public function getTemplateObject(){
        return $this->_template_object;
    }
    
    public function setDataObject($data){
        if(isset($data['footer_height']) && is_null($data['footer_height']))
            $data['footer_height'] = 20;
        $this->_data_object = $data;
        return $this;
    }
    
    public function getDataObject(){
        $obj = New Varien_Object();
        $obj->setData($this->_data_object);
        return $obj;
    }
}
