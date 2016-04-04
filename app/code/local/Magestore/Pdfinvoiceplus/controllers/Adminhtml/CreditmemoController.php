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
class Magestore_Pdfinvoiceplus_Adminhtml_CreditmemoController extends Mage_Adminhtml_Controller_Action
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
    public function printMassCreditmemoAction(){
        Mage::getSingleton('core/session')->setData('type','creditmemo'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('order_ids');
        //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
              //
        $creditmemoId = array();
        foreach($ids as $id){
            $order = Mage::getModel('sales/order')->load($id);
            if($order->hasCreditmemos()){
                foreach ($order->getCreditmemosCollection() as $creditmemoCollection){
                    $creditmemoId[] = $creditmemoCollection->getData('entity_id');
                }
            }
        }
        if(empty($creditmemoId)){
            $this->_redirect('adminhtml/sales_order');
            $error = Mage::helper('sales')->__('You have no files to get');
            Mage::getSingleton('core/session')->addError($error);
            return;
        }
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status',array('eq'=>1));
        if(!$template->getSize()){
            $this->_redirect('adminhtml/sales_order');
            $message = Mage::helper('sales')->__('Template not found');
            Mage::getSingleton('core/session')->addError($message);
        }else{
            $pdfData= Mage::getSingleton('pdfinvoiceplus/entity_masspdfcreditmemo')->getPdfDataCreditmemos($creditmemoId);
            $this->_prepareDownloadResponse('Creditmemo'.$date_curent.'.pdf',$pdfData,'application/pdf');
        }
        //end by zeus 08/01  
    }
    public function printMassCreditmemoGridAction(){
        Mage::getSingleton('core/session')->setData('type','creditmemo'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('creditmemo_ids');
        
          //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
		//zend_debug::dump($date_curent); die('vao day');
		// End edit Adminhtml/controller/sale/invoice
        
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status',array('eq'=>1));
        if(!$template->getSize()){
            $this->_redirect('adminhtml/sales_creditmemo');
            $message = Mage::helper('sales')->__('Template not found');
            Mage::getSingleton('core/session')->addError($message);
        }else{
            $pdfData = Mage::getSingleton('pdfinvoiceplus/entity_masspdfcreditmemo')->getPdfDataCreditmemos($ids);
            $this->_prepareDownloadResponse('Creditmemo'.$date_curent.'.pdf',$pdfData,'application/pdf');
        }
        // End edit Adminhtml/controller/sale/invoice
    }
    
    public function printAction(){
        Mage::getSingleton('core/session')->setData('type','creditmemo'); // Change By Jack 27/12
         if (!$creditmemoId = $this->getRequest()->getParam('creditmemo_id'))
        {
            return false;
        }
        try {
            $pdfFile = Mage::getSingleton('pdfinvoiceplus/entity_creditmemopdf')->getThePdf((int) $creditmemoId, false);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }       
    }
    
}