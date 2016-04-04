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
class Magestore_Pdfinvoiceplus_Block_Totals_Invoice extends Mage_Sales_Block_Order_Invoice_Totals
{
    protected function _prepareLayout() {
        parent::__construct();
        $taxBlock = $this->getLayout()->createBlock('pdfinvoiceplus/totals_tax')
                ->setTemplate('pdfinvoiceplus/tax/order/tax.phtml');
        $this->setChild('tax',$taxBlock);
    }
    public function getInvoice()
    {
       if ($this->_invoice === null) {
            if (Mage::registry('current_invoice')) {
                $this->_invoice = Mage::registry('current_invoice');
            } else {
                $invoiceId = $this->getRequest()->getParam('invoice_id');
                $this->_invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            }
        }
        return $this->_invoice;
    }
    
    public function getOrder()
    {
        return $this->getInvoice()->getOrder();
    }
}