<?php
class Magestore_Pdfinvoiceplus_InvoiceController extends Mage_Core_Controller_Front_Action{
    public function printAction(){
         Mage::getSingleton('core/session')->setData('type','invoice'); // Change By Jack 27/12
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if(!$invoiceId){
            return false;
        }
        try{
            $pdfFile = Mage::getSingleton('pdfinvoiceplus/entity_invoicepdf')->getThePdf((int) $invoiceId, false);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        }catch(Exception $e){
            Mage::log($e->getMessage());
            return;
        }
    }
//     public function printAction(){
//        $invoiceId = $this->getRequest()->getParam('invoice_id');
//        if(!$invoiceId){
//            return false;
//        }
//        try{
//            $check = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if($check->getData()){
//            $block = $this->getLayout()->createBlock('pdfinvoiceplus/pdf');
//            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
//            $pdfFile = $block->getInvoicePdf($invoice);
//            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
//                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
//            }else{
//                Mage::getSingleton('core/session')->addError('CAN NOT PRINT INVOICE NOW');
//                $this->_redirect('sales/order/invoice',array('order_id'=>$invoiceId));
//            }
//        }catch(Exception $e){
//            Mage::log($e->getMessage());
//            return;
//        }
//    }
}
?>