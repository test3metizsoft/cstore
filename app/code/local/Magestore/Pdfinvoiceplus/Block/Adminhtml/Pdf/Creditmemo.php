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
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdf_Creditmemo extends Mage_Adminhtml_Block_Sales_Items_Abstract {

    public function __construct() {
        parent::__construct();
        $this->addItemRender('default','adminhtml/sales_items_renderer_default','pdfinvoiceplus/sales/order/creditmemo/items/renderer/default.phtml');
        $this->addItemRender('bundle','bundle/adminhtml_sales_order_items_renderer','pdfinvoiceplus/sales/bundle/creditmemo/items/renderer/renderer.phtml');
        $this->addColumnRender('qty','adminhtml/sales_items_column_qty','sales/items/column/qty.phtml');
        $this->addColumnRender('name','adminhtml/sales_items_column_name','sales/items/column/name.phtml');
        $this->addColumnRender('name','adminhtml/sales_items_column_name_grouped','sales/items/column/name.phtml','grouped');
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

    public function getInvoice() {
        if (Mage::registry('current_invoice'))
            return Mage::registry('current_invoice');
        else {
            $invoiceId = $this->getRequest()->getParam('invoice_id');
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            return $invoice;
        }
    }

    public function getCreditmemo() {
        if (Mage::registry('current_creditmemo'))
            return Mage::registry('current_creditmemo');
        else {
            $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
            return $creditmemo;
        }
    }

    public function getOrder() {
        if (Mage::registry('current_order'))
            return Mage::registry('current_order');
        else {
            $order = $this->getCreditmemo ()->getOrder();
            Mage::register('current_order', $order);
            return $order;
        }
    }

    public function getItemsCollection() {
        return $this->getOrder()->getItemsCollection();
    }

    public function getCreditmemoPdf($creditmemo) {
        $this->setSource($creditmemo);
        $templateCode = Mage::helper('pdfinvoiceplus/pdf')->getTemplateCode();
        $this->setTemplate('pdfinvoiceplus/templates/' . $templateCode . '/creditmemo.phtml');
        $creditmemoPdf = Mage::getModel('pdfinvoiceplus/entity_creditmemo');
        return $creditmemoPdf->getPdf($this->toHtml());
    }

}
