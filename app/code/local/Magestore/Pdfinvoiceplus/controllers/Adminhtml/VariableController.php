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
 * Pdfinvoiceplus Block
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Adminhtml_VariableController extends Mage_Adminhtml_Controller_Action
{
    /**
     * WYSIWYG Plugin Action
     *
     */
    public function wysiwygPluginOrderAction()
    {
        $type = 'order';
        $customVariables = Mage::getModel('pdfinvoiceplus/variables_process')->getVariablesOptionArray($type,true);
        $this->getResponse()->setBody(Zend_Json::encode($customVariables));
    }
    
    public function wysiwygPluginInvoiceAction()
    {
        $type = 'invoice';
        $customVariables = Mage::getModel('pdfinvoiceplus/variables_process')->getVariablesOptionArray($type,true);
        $this->getResponse()->setBody(Zend_Json::encode($customVariables));
    }
    
    public function wysiwygPluginCreditmemoAction()
    {
        $type = 'creditmemo';
        $customVariables = Mage::getModel('pdfinvoiceplus/variables_process')->getVariablesOptionArray($type,true);
        $this->getResponse()->setBody(Zend_Json::encode($customVariables));
    }
    
    /**
     * v2.0
     */
    
    public function orderAction(){
        $variables = Mage::getModel('pdfinvoiceplus/variables')->getOrderVarsData();
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
    
    public function invoiceAction(){
        $variables = Mage::getModel('pdfinvoiceplus/variables')->getInvoiceVarsData();
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
    
    public function creditmemoAction(){
        $variables = Mage::getModel('pdfinvoiceplus/variables')->getCreditmemoVarsData();
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
    
    
    public function allVarsAction()
    {
        $this->getResponse()->setBody(Zend_Json::encode(''));
    }
    
}

