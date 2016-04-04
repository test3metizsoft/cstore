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
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Totals_Invoice extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals {

    public function __construct() {
        $this->_beforeToHtml();
        parent::_construct();
    }
    
    public function getSource(){
        return Mage::registry('source_totals');
    }

//    protected function _prepareLayout() {
//        parent::__construct();
//        $taxBlock = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_totals_tax')
//                ->setTemplate('pdfinvoiceplus/sales/totals/tax.phtml');
//        $this->setChild('tax',$taxBlock);
//    }
//    
//    public function getSource(){
//        return $this->getInvoice();
//    }
//    
//    public function getInvoice()
//    {
//        if ($this->_invoice === null) {
//            if (Mage::registry('current_invoice')) {
//                $this->_invoice = Mage::registry('current_invoice');
//            } elseif ($this->getParentBlock() && $this->getParentBlock()->getInvoice()) {
//                $this->_invoice = Mage::getModel('sales/order_invoice')
//                        ->load($this->getRequest()->getParam('invoice_id'));
//            }else{
//                $invoiceId = $this->getRequest()->getParam('invoice_id');
//                $this->_invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
//                Mage::register('current_invoice', $this->_invoice);
//            }
//        }
//        return $this->_invoice;
//    }
//    
//    public function getOrder()
//    {
//        return $this->getInvoice()->getOrder();
//    }
}
