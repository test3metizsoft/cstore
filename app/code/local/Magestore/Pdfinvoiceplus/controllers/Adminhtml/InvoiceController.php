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
 * Pdfinvoiceplus Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Adminhtml_InvoiceController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Pdfinvoiceplus_Adminhtml_PdfinvoiceplusController
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('pdfinvoiceplus/pdfinvoiceplus')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('pdfinvoiceplus');
    }

//    public function printAction(){
//        if(!$invoiceId = $this->getRequest()->getParam('invoice_id')){
//            return false;
//        }
//        try{
//            $check = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if($check->getData()){
//            $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_pdf');
//            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
//            if($invoice->getId())
//                Mage::register('current_invoice', $invoice);
//                $pdfFile = $block->getInvoicePdf();
//                $this->_prepareDownloadResponse($pdfFile->getData('filename') .
//                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
//            }else{
//                Mage::getSingleton('adminhtml/session')->addError('Can not print invoice because no template is active.');
//                $this->_redirect('adminhtml/sales_order_invoice/view',array('invoice_id'=>$invoiceId));
//            }
//        }catch(Exception $e){
//            Mage::log($e->getMessage());
//            return;
//        }
//    }

    public function testAction() {
        $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_pdf');
        $invoice = Mage::getModel('sales/order_invoice')->load(24);
        $block->setSource($invoice)
            ->setTemplate('pdfinvoiceplus/templates/template02/invoice.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function printMassInvoiceAction() {
        Mage::getSingleton('core/session')->setData('type','invoice'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('order_ids');
        $invoiceId = array();
        foreach ($ids as $id) {
            $order = Mage::getModel('sales/order')->load($id);
            if ($order->hasInvoices()) {
                foreach ($order->getInvoiceCollection() as $invoiceCollection) {
                    $invoiceId[] = $invoiceCollection->getData('entity_id');
                }
            }
        }
        //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
              //
        if (empty($invoiceId)) {
            $this->_redirect('adminhtml/sales_order');
            $error = Mage::helper('sales')->__('You have no files to get');
            Mage::getSingleton('core/session')->addError($error);
            return;
        }
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status', array('ep' => 1));

        if (!$template->getSize()) {
            $this->_redirect('adminhtml/sales_order');
            $message = Mage::helper('sales')->__('Template not found');
            Mage::getSingleton('core/session')->addError($message);
        } else {
            $pdfData = Mage::getSingleton('pdfinvoiceplus/entity_masspdfinvoice')->getPdfDataInvoice($invoiceId);
            $this->_prepareDownloadResponse('Invoice' .$date_curent.
                '.pdf', $pdfData, 'application/pdf');
        }
//        }else{
//             foreach ($ids as $invoiceId){
//            $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_pdf');
//            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
//            if($invoice->getId()){
//                        Mage::register('current_invoice', $invoice);
//                        if(!isset($pdf)){
//                           $pdf = $block->getInvoicePdf();
//                        }
//                    }
//                }
//             $this->_prepareDownloadResponse($pdf->getData('filename') .
//                    '.pdf', $pdf->getData('pdfbody'), 'application/pdf');
//        }
    //end by zeus 08/01  
        
        }

    public function printMassInvoiceGridAction() {
        Mage::getSingleton('core/session')->setData('type','invoice'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('invoice_ids');
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status', array('eq' => 1));
        //zend_Debug::Dump($template->getSize());die();
        //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
		//zend_debug::dump($date_curent); die('vao day');
		//End edit Adminhtml/controller/sale/invoice
        
        if (!$template->getSize()) {
            $this->_redirect('adminhtml/sales_invoice');
            $message = Mage::helper('sales')->__('Template not found');
            Mage::getSingleton('core/session')->addError($message);
        } else {
            $pdfData = Mage::getSingleton('pdfinvoiceplus/entity_masspdfinvoice')->getPdfDataInvoice($ids);

            $this->_prepareDownloadResponse('Invoice' .$date_curent.
                '.pdf', $pdfData, 'application/pdf');
        }
        //End edit Adminhtml/controller/sale/invoice
    }

    public function printAction() {
        Mage::getSingleton('core/session')->setData('type','invoice'); // Change By Jack 27/12
        if (!$invoiceId = $this->getRequest()->getParam('invoice_id')) {
            return false;
        }
        try {
            $pdfFile = Mage::getSingleton('pdfinvoiceplus/entity_invoicepdf')->getThePdf((int) $invoiceId, false);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
    }

}
