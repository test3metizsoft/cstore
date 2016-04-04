<?php

class Magestore_Pdfinvoiceplus_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Pdfinvoiceplus_Adminhtml_PdfinvoiceplusController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('pdfinvoiceplus/pdfinvoiceplus')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('pdfinvoiceplus');
    }
    
//    public function printAction(){
//        $orderId = $this->getRequest()->getParam('order_id');
//        if(!$orderId){
//            return false;
//        }
//        try{
//            $check = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if($check->getId()){
//                $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_pdf');
//                $order = Mage::getModel('sales/order')->load($orderId);
//                $pdfFile = $block->getOrderPdf($order);
//                $this->_prepareDownloadResponse($pdfFile->getData('filename') .
//                        '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
//            }else{
//                Mage::getSingleton('adminhtml/session')->addError('Can not print order because no template is active.');
//                $this->_redirect('adminhtml/sales_order/view',array('order_id'=>$orderId));
//            }
//        }catch(Exception $e){
//            Mage::log($e->getMessage());
//            return;
//        }
//    }
    public function printMassOrderAction(){
        Mage::getSingleton('core/session')->setData('type','order'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('order_ids');
        //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
		//zend_debug::dump($date_curent); die('vao day');
		
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status',array('eq'=>1));
        if(!$template->getSize()){
           $this->_redirect('adminhtml/sales_order'); 
           $message = Mage::helper('sales')->__('Template not found');
           Mage::getSingleton('core/session')->addError($message);
        }else{
            $pdfData = Mage::getSingleton('pdfinvoiceplus/entity_masspdforder')->getPdfDataOrder($ids);
           $this->_prepareDownloadResponse('Order' .$date_curent.
                   '.pdf', $pdfData, 'application/pdf');
        }
        // End edit Adminhtml/controller/sale/invoice
    }
    
     public function printAction(){
         Mage::getSingleton('core/session')->setData('type','order'); // Change By Jack 27/12
         if (!$orderId = $this->getRequest()->getParam('order_id'))
        {
            return false;
        }
        try {
            $pdfFile = Mage::getSingleton('pdfinvoiceplus/entity_orderpdf')->getThePdf((int) $orderId, false);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }       
    }
}

