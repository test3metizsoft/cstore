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
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Design_Editor extends Mage_Core_Block_Template
{
    protected $_print_type = 'invoice';
    protected $_template_html;
    protected $_template_object;
    
    /**
     * set type one of three invoice, order, creditmemo
     */
    public function setPrintType($type_name){
        $this->_print_type = $type_name;
        return $this;
    }
    
    public function setTemplateHtml($html){
        $this->_template_html = $html;
        return $this;
    }

    public function getTemplateHtml(){
        return $this->_template_html;
    }
    
    public function setTemplateObject($model){
        $this->_template_object = $model;
        return $this;
    }
    
    public function getTemplateId(){
        if(is_object($this->_template_object)){
            return $this->_template_object->getId();
        }
        return '';
    }


    public function getPrintSize(){
        $obj = $this->_template_object;
        if($obj){
            switch ($obj->getFormat()){
                case 'Letter':
                    return '1024px';
                case 'A4':
                    return '1024px';
                case 'A5':
                    return '1024px';
                case 'A6':
                    return '1024px';
                case 'A7':
                    return '1024px';
            }
            
        }else{
            return '1024px';
        }
    }
    
    public function getBackUrl(){
        $type = $this->getRequest()->getParam('type');
        
        $id = $this->getRequest()->getParam('id');
        /* Change by Zeus 02/12 */
        if($type == 'order')
            return Mage::getSingleton('adminhtml/url')->getUrl('pdfinvoiceplusadmin/adminhtml_design/editOrder', array('id'=>$id));
        elseif($type == 'invoice')
            return Mage::getSingleton('adminhtml/url')->getUrl('pdfinvoiceplusadmin/adminhtml_design/editInvoice', array('id'=>$id));
        elseif($type == 'creditmemo')
            return Mage::getSingleton('adminhtml/url')->getUrl('pdfinvoiceplusadmin/adminhtml_design/editCreditmemo', array('id'=>$id));
        /* End change */
    }
    
    public function getHtmlEdit(){
        $template = Mage::getModel('pdfinvoiceplus/template')->load($this->getRequest()->getParam('id'));
        if($type = $this->getTypeHtmlEdit()){
            if($type == 'order')
                return $template->getOrderHtml();
            elseif($type == 'invoice')
                return $template->getInvoiceHtml();
            elseif($type == 'creditmemo')
                return $template->getCreditmemoHtml();
        }
        return '';
    }
}