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
 * SyncInfoController Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Adminhtml_SyncInfoController extends Mage_Adminhtml_Controller_Action {

    /**
     * this function to be use for client load from database
     * @return JSON send to web browser or anny client
     */
    public function loadAction() {
        
    }
    
    public function updateAction() {
        $id = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        Mage::getModel('pdfinvoiceplus/syncInfo')->syncExecution($id, $type);
        /* Chang by Zeus 02/12 */
        switch ($type){
            case 'order':
                $this->_redirect('pdfinvoiceplusadmin/adminhtml_design/editOrder', array('id' => $id));
                return;
            case 'invoice':
                $this->_redirect('pdfinvoiceplusadmin/adminhtml_design/editInvoice', array('id' => $id));
                return;
            case 'creditmemo':
                $this->_redirect('pdfinvoiceplusadmin/adminhtml_design/editCreditmemo', array('id' => $id));
                return;
            default:
                return;
        }
        /* End change */
    }
    public function resetAction() {
        $id = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        Mage::getModel('pdfinvoiceplus/syncInfo')->resetTemplate($id, $type);
        /* Chang by Zeus 02/12 */
        switch ($type){
            case 'order':
                $this->_redirect('pdfinvoiceplusadmin/adminhtml_design/editOrder', array('id' => $id));
                return;
            case 'invoice':
                $this->_redirect('pdfinvoiceplusadmin/adminhtml_design/editInvoice', array('id' => $id));
                return;
            case 'creditmemo':
                $this->_redirect('pdfinvoiceplusadmin/adminhtml_design/editCreditmemo', array('id' => $id));
                return;
            default:
                return;
        }
        /* End change */
    }

    /*
     * this function to be use for client update to database
     * easy use like API
     */
//old version
//    public function updateAction() {
//        $id = $this->getRequest()->getParam('id');
//        $data = $this->getRequest()->getPost();
//        $data['company_logo'] = ''; //reset logo name
//        $isDelete = false;
//        if ($data) {
//            $path = '';
//            $template = Mage::getModel('pdfinvoiceplus/template')->load($id);
//            if (isset($data['company_logo_delete']) && $data['company_logo_delete']) {
//                $data['company_logo'] = '';
//                $isDelete = true;
//            } else {
//                if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
//                    try {
//                        /* Starting upload */
//                        $uploader = new Varien_File_Uploader('company_logo');
//
//                        // Any extention would work
//                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
//                        $uploader->setAllowRenameFiles(false);
//
//                        // Set the file upload mode 
//                        // false -> get the file directly in the specified folder
//                        // true -> get the file in the product like folders 
//                        //    (file.jpg will go in something like /media/f/i/file.jpg)
//                        $uploader->setFilesDispersion(true);
//
//                        // We set media as the upload dir
//                        $path = Mage::getBaseDir('media') . DS . 'magestore' . DS . 'pdfinvoiceplus' . DS . 'logo';
//                        $result = $uploader->save($path, $_FILES['company_logo']['name']);
//                        $data['company_logo'] = $result['file'];
//                    } catch (Exception $e) {
//                        $data['company_logo'] = $_FILES['company_logo']['name'];
//                    }
//                }else{
//                    $data['company_logo'] = $template->getCompanyLogo();
//                }
//            }
//            
//            $data['order_html'] = $template->getOrderHtml();
//            $data['invoice_html'] = $template->getInvoiceHtml();
//            $data['creditmemo_html'] = $template->getCreditmemoHtml();
//            $data['company_logo'] = $template->getCompanyLogo();
//            
//            if($isDelete){
//                $path = '';
//            }else{
//                $path = Mage::getBaseUrl('media').'magestore/pdfinvoiceplus/logo'.$data['company_logo'];
//            }
//            if(!$data['company_logo'] && !$isDelete){
//                unset($data['company_logo']);
//            }
//            
//            Mage::getModel('pdfinvoiceplus/syncInfo')->syncExecution($data, $id);
//            
//            if(!isset($data['company_logo']) && !$isDelete){
//                echo json_encode(array('success'=>'2', 'message'=>'no change image'));
//            }else{
//                if(!isset($data['company_logo'])){
//                    $data['company_logo'] = '';
//                }
//                echo json_encode(array('success'=>'1',
//                    'image'=>array('name'=>$data['company_logo'], 'path'=> $path))
//                );
//            }
//        }else{
//            echo json_encode(array('success'=>'0', 'message'=>'no data'));
//        }
//    }

}
