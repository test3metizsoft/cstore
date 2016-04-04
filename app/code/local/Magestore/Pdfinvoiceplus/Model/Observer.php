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
 * Pdfinvoiceplus Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_Pdfinvoiceplus_Model_Observer
     */
    public function controllerActionPredispatch($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }
    // add by Jack
    public function orderManagerObserver($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $controllerName = $block->getRequest()->getControllerName();
        if($controllerName == 'sales_order')
            return $this->_order($block); 
        else if($controllerName == 'sales_order_invoice' || $controllerName == 'sales_invoice')
            return $this->_invoice($block);
        else if($controllerName == 'sales_order_creditmemo' || $controllerName == 'sales_creditmemo')
           return $this->_creditmemo($block); 
    }
    private function helper(){
        return Mage::helper('adminhtml');
    }
    public function isRemovePrintDefault(){
        return Mage::helper('pdfinvoiceplus')->isRemovePrintDefault();
    }
      
    private function _order($block)
    {
        $action = $block->getRequest()->getActionName();
        if(Mage::helper('pdfinvoiceplus')->checkEnable()){
            if($action == 'index'){
                $massactionBlock = $block->getMassactionBlock();
                if($massactionBlock){
                    $massactionBlock->addItem(
                    'pdforder', array('label' => $this->helper()->__('Print Orders via PDF Invoice+'),
                    'url' => $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_order/printmassorder') //this should be the url where there will be mass operation
                        )
                    );
                     $massactionBlock->addItem(
                        'pdfinvoice', array('label' => $this->helper()->__('Print Invoices via PDF Invoice+'),
                        'url' => $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_invoice/printmassinvoice') //this should be the url where there will be mass operation
                            )
                    );
                    $massactionBlock->addItem(
                        'pdfcreditmemo', array('label' => $this->helper()->__('Print Credit Memos via PDF Invoice+'),
                        'url' => $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_creditmemo/printmasscreditmemo') //this should be the url where there will be mass operation
                            )
                    );
                // Change By Jack 23/01/2015
                    if($this->isRemovePrintDefault()){
                        $massactionBlock->removeItem('pdfinvoices_order');
                        $massactionBlock->removeItem('pdfcreditmemos_order');
                        $massactionBlock->removeItem('pdfdocs_order');
                    }    
                //  
                }
                return true;
            }
            $orderBlock = get_class($block);
            if($action == 'view' && method_exists($orderBlock,'addButton')){
                $enable = Mage::helper('pdfinvoiceplus')->checkEnable();
                $active = Mage::helper('pdfinvoiceplus')->checkStoreTemplate();
                if($enable == 1 && $active == 1){
                    $block->addButton('printpdf', array(
                        'label' => $this->helper()->__('Print Custom PDF File'),
                        'class' => 'save',
                        'onclick' => $this->getPrintPdfLink('order')
                        )
                    );
                }
                return true;
            }
        }
        return false;
    }
    private function _invoice($block)
    {
        $action = $block->getRequest()->getActionName();
         if(Mage::helper('pdfinvoiceplus')->checkEnable()){
             if($action == 'index'){
                $massactionBlock = $block->getMassactionBlock();
                if($massactionBlock){
                    $massactionBlock->addItem(
                       'pdfinvoicegrid', array('label' => $this->helper()->__('Print Invoices via PDF Invoice+'),
                       'url' => $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_invoice/printmassinvoicegrid') //this should be the url where there will be mass operation
                           )
                    );
                // Change By Jack 23/01/2015
                    if($this->isRemovePrintDefault()){
                        $massactionBlock->removeItem('pdfinvoices_order');
                    }    
                //  
                }
                return true;
             }
              $invoiceBlock = get_class($block);
             if($action == 'view' && method_exists($invoiceBlock,'addButton')){
                $enable = Mage::helper('pdfinvoiceplus')->checkEnable();
                $active = Mage::helper('pdfinvoiceplus')->checkStoreTemplate();
                if($enable == 1 && $active == 1){
                    $block->addButton('printpdf', array(
                        'label' => $this->helper()->__('Print Custom PDF File'),
                        'class' => 'save',
                        'onclick' => $this->getPrintPdfLink('invoice')
                        )
                    );
                    // Change By Jack 23/01/2015
                     if($this->isRemovePrintDefault()){
                        $block->removeButton('print');
                    }   
                    //
                }
                return true;
             }
        }
    } 
     private function _creditmemo($block)
    {
        $action = $block->getRequest()->getActionName();
         if(Mage::helper('pdfinvoiceplus')->checkEnable()){
             if($action == 'index'){
                $massactionBlock = $block->getMassactionBlock();
                if($massactionBlock){
                    $massactionBlock->addItem(
                       'pdfinvoicegrid', array('label' => $this->helper()->__('Print Credit Memos via PDF Invoice+'),
                       'url' => $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_creditmemo/printmasscreditmemogrid') //this should be the url where there will be mass operation
                           )
                    );
                    // Change By Jack 23/01/2015
                    if($this->isRemovePrintDefault()){
                        $massactionBlock->removeItem('pdfcreditmemos_order');
                    }    
                    //  
                }
                return true;
             }
             $creditmemoBlock = get_class($block);
             if($action == 'view' && method_exists ($creditmemoBlock,'addButton')){
                $enable = Mage::helper('pdfinvoiceplus')->checkEnable();
                $active = Mage::helper('pdfinvoiceplus')->checkStoreTemplate();
                if($enable == 1 && $active == 1){
                    $block->addButton('printpdf', array(
                        'label' => $this->helper()->__('Print Custom PDF File'),
                        'class' => 'save',
                        'onclick' => $this->getPrintPdfLink('creditmemo')
                        )
                    );
                     // Change By Jack 23/01/2015
                     if($this->isRemovePrintDefault()){
                        $block->removeButton('print');
                    }   
                    //
                }
                return true;
             }
        }
    } 
    private function getPrintPdfLink($type)
    {
        $templateCollection = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        $templateCollection->addFieldToSelect('*')
                ->addFieldToFilter('is_active', 1);

        $templateId = $templateCollection->getFirstItem()->getId();

        if (!empty($templateId))
        {
            return $location = 'setLocation(\'' . $this->getCustomPrintUrl($type) . '\')';
        }
        $messege = Mage::helper('pdfinvoiceplus')->__('You do not have a template selected for this invoice. You will get the default Magento Invoice');
        return $location = 'setLocation(\'' . $this->getCustomPrintUrl($type) . '\')';
    }
    
    public function getCustomPrintUrl($type)
    {
        $orderId = Mage::app()->getRequest()->getParam('order_id');    
        $invoiceId = Mage::app()->getRequest()->getParam('invoice_id'); 
        $creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id'); 
        if($type == 'order')
            return $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_order/print', array(
                        'order_id' => $orderId
                    ));
        if($type == 'invoice')
            return $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_invoice/print', array(
                        'invoice_id' => $invoiceId
                    ));
        if($type == 'creditmemo')
            return $this->helper()->getUrl('pdfinvoiceplusadmin/adminhtml_creditmemo/print', array(
                        'creditmemo_id' => $creditmemoId
                    ));
    }
    // end add 
}