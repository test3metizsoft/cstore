<?php
class Magestore_Pdfinvoiceplus_CreditmemoController extends Mage_Core_Controller_Front_Action{
    public function printAction(){
         Mage::getSingleton('core/session')->setData('type','creditmemo'); // Change By Jack 27/12
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if(!$creditmemoId){
            return false;
        }
        try{
            $pdfFile = Mage::getSingleton('pdfinvoiceplus/entity_creditmemopdf')->getThePdf((int) $creditmemoId, false);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        }catch(Exception $e){
            Mage::log($e->getMessage());
            return;
        } 
    }
//    public function printAction(){
//        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
//        if(!$creditmemoId){
//            return false;
//        }
//        try{
//            $check = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if($check->getData()){
//            $block = $this->getLayout()->createBlock('pdfinvoiceplus/pdf');
//            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
//            $pdfFile = $block->getCreditmemoPdf($creditmemo);
//            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
//                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
//            }else{
//                Mage::getSingleton('core/session')->addError('CAN NOT PRINT MEMO NOW');
//                $this->_redirect('sales/order/creditmemo',array('order_id'=>$creditmemoId));
//            }
//        }catch(Exception $e){
//            Mage::log($e->getMessage());
//            return;
//        }
//    }
}

?>