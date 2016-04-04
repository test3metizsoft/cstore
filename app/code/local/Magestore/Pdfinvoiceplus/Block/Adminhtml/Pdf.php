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
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdf extends Mage_Adminhtml_Block_Sales_Items_Abstract {

    public function __construct() {
        parent::__construct();
        if ($this->getInvoice()->getId()) {
            $this->addItemRender('default', 'adminhtml/sales_items_renderer_default', 'pdfinvoiceplus/sales/order/invoice/items/renderer/default.phtml');
            $this->addItemRender('bundle', 'bundle/adminhtml_sales_order_items_renderer', 'pdfinvoiceplus/sales/bundle/invoice/items/renderer/renderer.phtml');
        } elseif ($this->getCreditmemo()->getId()) {
            $this->addItemRender('default', 'adminhtml/sales_items_renderer_default', 'pdfinvoiceplus/sales/order/creditmemo/items/renderer/default.phtml');
        } else {
            $this->addItemRender('default', 'adminhtml/sales_order_view_items_renderer_default', 'pdfinvoiceplus/sales/order/view/items/renderer/default.phtml');
            $this->addItemRender('bundle', 'bundle/adminhtml_sales_order_view_items_renderer', 'pdfinvoiceplus/sales/bundle/view/items/renderer/renderer.phtml');
        }

        $this->addColumnRender('qty', 'adminhtml/sales_items_column_qty', 'pdfinvoiceplus/sales/items/column/qty.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name', 'sales/items/column/name.phtml');
        $this->addColumnRender('name', 'adminhtml/sales_items_column_name_grouped', 'sales/items/column/name.phtml', 'grouped');
    }

    protected function _prepareLayout() {
        if ($this->getInvoice()->getId())
            $this->setChild('invoice_totals', $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_totals_invoice'));
        if ($this->getOrder()->getId())
            $this->setChild('order_totals', $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_totals_order'));
        if ($this->getCreditmemo()->getId())
            $this->setChild('creditmemo_totals', $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_totals_creditmemo'));
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
            $orderId = $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
            return $order;
        }
    }

    public function getItemsCollection() {
        return $this->getOrder()->getItemsCollection();
    }

    public function getInvoicePdf() {
        $templateCode = Mage::helper('pdfinvoiceplus/pdf')->getTemplateCode();
        $this->setTemplate('pdfinvoiceplus/templates/' . $templateCode . '/invoice.phtml');
        $invoicePdf = Mage::getModel('pdfinvoiceplus/entity_invoice');
        return $invoicePdf->getPdf($this->toHtml());
    }

    public function getOrderPdf($order) {
        $this->setSource($order);
        $templateCode = Mage::helper('pdfinvoiceplus/pdf')->getTemplateCode();
        $this->setTemplate('pdfinvoiceplus/templates/' . $templateCode . '/order.phtml');
        $orderPdf = Mage::getModel('pdfinvoiceplus/entity_order');
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        return $orderPdf->getPdf($template->getOrderHtml());
    }

    public function getCreditmemoPdf($creditmemo) {
        $this->setSource($creditmemo);
        $templateCode = Mage::helper('pdfinvoiceplus/pdf')->getTemplateCode();
        $this->setTemplate('pdfinvoiceplus/templates/' . $templateCode . '/creditmemo.phtml');
        $creditmemoPdf = Mage::getModel('pdfinvoiceplus/entity_creditmemo');
        return $creditmemoPdf->getPdf($this->toHtml());
    }

    public function getItemHtml(Varien_Object $item) {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }
        $html = $this->getItemRenderer($type)
            ->setItem($item)
            ->toHtml();
        return $html;
    }

}
