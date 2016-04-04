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
class Magestore_Pdfinvoiceplus_Adminhtml_DesignController extends Mage_Adminhtml_Controller_Action {

    /**
     * edit design for invoice
     */
    public function editInvoiceAction() {
        $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_editor')
            ->setTemplate('pdfinvoiceplus/editor.phtml');
        $block->setPrintType('invoice');
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        Mage::register('pdfinvoiceplus_template', $model);
        $block->setTemplateHtml($model->getInvoiceHtml())
            ->setTemplateObject($model);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function editOrderAction() {
        $id = $this->getRequest()->getParam('id');
        $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_editor')
            ->setTemplate('pdfinvoiceplus/editor.phtml');
        $block->setPrintType('order');
        $model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        Mage::register('pdfinvoiceplus_template', $model);
        $block->setTemplateHtml($model->getOrderHtml())->setTemplateObject($model);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function editCreditmemoAction() {
        $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_editor')
            ->setTemplate('pdfinvoiceplus/editor.phtml');
        $block->setPrintType('creditmemo');
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        Mage::register('pdfinvoiceplus_template', $model);
        $block->setTemplateHtml($model->getCreditmemoHtml())->setTemplateObject($model);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function loadTemplateHtmlAction() {
        $post = $this->getRequest()->getPost();
        $id = $post['system_template_id'];
        $templateCode = Mage::getModel('pdfinvoiceplus/systemtemplate')->load($id)->getTemplateCode();
        $locale = $post['locale'];
        $form_data = $post; //['edit_form'];
        //$form_data = array();
        //parse_str($edit_form, $form_data); //data for template
        
        $data = $post;

        //upload file
        if (isset($data['company_logo_delete']) && $data['company_logo_delete']) {
            $data['company_logo'] = null;
        } else {
            if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
                try {
                    
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('company_logo');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(true);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . 'magestore' . DS . 'pdfinvoiceplus' . DS . 'logo';
                    $result = $uploader->save($path, $_FILES['company_logo']['name']);
                    $data['company_logo'] = $result['file'];
                } catch (Exception $e) {
                    $data['company_logo'] = $_FILES['company_logo']['name'];
                }
            }
        }
        
        if($data['company_logo']){
            $form_data['company_logo'] = $data['company_logo'];
        }else{
            $form_data['company_logo'] = $form_data['company_logo_hidden'];
        }
        
        $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_loadtemplate');
        $block->setLocale($locale)->setDataObject($form_data);

        $block->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/invoice.phtml');
        $result["invoice"] = $block->toHtml(); //htmlspecialchars(utf8_encode($block->toHtml()));
        $block->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/order.phtml');
        $result["order"] = $block->toHtml(); //htmlspecialchars(utf8_encode($block->toHtml()));
        $block->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/creditmemo.phtml');
        $result["creditmemo"] = $block->toHtml(); //htmlspecialchars(utf8_encode($block->toHtml()));
        //$this->getResponse()->setHeader('Content-type', 'application/json', true);
        //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        
        echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT);
        exit;
    }

    public function edithtmlAction() {
        $type = $this->getRequest()->getParam('type');
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $block = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_editor')
                ->setTypeHtmlEdit($type)
                ->setTemplate('pdfinvoiceplus/edithtml.phtml');
        //$head = $this->getLayout()->getBlock('head');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function savehtmlAction() {
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('pdfinvoiceplus/template')->load($data['id']);
        if ($data['type'] == 'invoice' && !empty($data['html'])) {
            $model->setInvoiceHtml($data['html']);
        } else if ($data['type'] == 'order' && !empty($data['html'])) {
            $model->setOrderHtml($data['html']);
        } else if ($data['type'] == 'creditmemo' && !empty($data['html'])) {
            $model->setCreditmemoHtml($data['html']);
        }
        $model->save();
    }

    public function previewOrderDesignAction() {
        Mage::getSingleton('core/session')->setData('type','order');
        $id = $this->getRequest()->getParam('id');
        //$model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        //load order
        $order = Mage::getModel('sales/order')->getCollection()->getFirstItem();
        if($order->getId())
            $order_id = $order->getId();
        else
            $order_id = -1;
        $model = Mage::getModel('pdfinvoiceplus/entity_orderpdf');
        $model->setTheOrder($order);
        $model->templateId = $id;
        $model->orderId = $order_id;
        if(!Mage::registry('current_order')){
            Mage::register('current_order', $order);
        }
        
        $preg = "/(\s)contenteditable(\s*)=(\s*)[\"']?true[\"']?/i"; //remove contenteditable=true
        $html  = '<script src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'magestore/pdfinvoiceplus/jquery-1.10.2.js" type="text/javascript" ></script>';
        $html .= '<style>body{position: relative; border: 1px solid #c9c9c9;}</style>';
        $html .= '<style>div, p, span, th, td {word-wrap: break-word;word-break: break-all;}</style>'; //reset css
        
        if($order_id == -1){
            $html .= $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_preview')
                ->setTemplate('pdfinvoiceplus/previewdesign/no_order.phtml')->toHtml();
        }else{
            $html .= preg_replace($preg, " ", $model->toHtml());
        }
        
        //add js
        $html .= $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_preview')->toHtml();
        
        $this->getResponse()->setBody($html);
    }

    public function previewInvoiceDesignAction() {
        Mage::getSingleton('core/session')->setData('type','invoice');
        $id = $this->getRequest()->getParam('id');
        //$model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        
        //load invoice
        $invoice = Mage::getModel('sales/order_invoice')->getCollection()->getFirstItem();
        if($invoice->getId())
            $invoice_id = $invoice->getId();
        else
            $invoice_id = -1;
        $order = $invoice->getOrder();
        $model = Mage::getModel('pdfinvoiceplus/entity_invoicepdf');
        $model->setOrder($order);
        $model->templateId = $id;
        $model->invoiceId = $invoice_id;
        if(!Mage::registry('current_order')){
            Mage::register('current_order', $order);
        }
        if(!Mage::registry('current_invoice')){
            Mage::register('current_invoice', $invoice);
        }
        
        $preg = "/(\s)contenteditable(\s*)=(\s*)[\"']?true[\"']?/i"; //remove contenteditable=true
        $html  = '<script src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'magestore/pdfinvoiceplus/jquery-1.10.2.js" type="text/javascript" ></script>';
        $html .= '<style>body{position: relative; border: 1px solid #c9c9c9;}</style>';
        $html .= '<style>div, p, span, th, td {word-wrap: break-word;word-break: break-all;}</style>'; //reset css
        
        if($invoice_id == -1){
            $html .= $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_preview')
                ->setTemplate('pdfinvoiceplus/previewdesign/no_invoice.phtml')->toHtml();
        }else{
            $html .= preg_replace($preg, " ", $model->toHtml());
        }
        //add js
        $html .= $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_preview')->toHtml();
        
        $this->getResponse()->setBody($html);
    }

    public function previewCreditmemoDesignAction() {
        Mage::getSingleton('core/session')->setData('type','creditmemo');
        $id = $this->getRequest()->getParam('id');
        //$model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        
        //load invoice
        $creditmemo = Mage::getModel('sales/order_creditmemo')->getCollection()->getFirstItem();
        if($creditmemo->getId()){
            $creditmemo_id = $creditmemo->getId();
        }else{
            $creditmemo_id = -1;
        }
        $order = $creditmemo->getOrder();
        $model = Mage::getModel('pdfinvoiceplus/entity_creditmemopdf');
        $model->setOrder($order);
        $model->templateId = $id;
        $model->creditmemoId = $creditmemo_id;
        if(!Mage::registry('current_order')){
            Mage::register('current_order', $order);
        }
        if(!Mage::registry('current_creditmemo')){
            Mage::register('current_creditmemo', $creditmemo);
        }
        
        $preg = "/(\s)contenteditable(\s*)=(\s*)[\"']?true[\"']?/i"; //remove contenteditable=true
        $html  = '<script src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'magestore/pdfinvoiceplus/jquery-1.10.2.js" type="text/javascript" ></script>';
        $html .= '<style>body{position: relative; border: 1px solid #c9c9c9;}</style>';
        $html .= '<style>div, p, span, th, td {word-wrap: break-word;word-break: break-all;}</style>'; //reset css
        
        if($creditmemo_id == -1){
            $html .= $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_preview')
                ->setTemplate('pdfinvoiceplus/previewdesign/no_creditmemo.phtml')->toHtml();
        }else{
            $html .= preg_replace($preg, " ", $model->toHtml());
        }
        
        //add js
        $html .= $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_preview')->toHtml();
        
        $this->getResponse()->setBody($html);
    }

    
//    public function uploadlogoAction(){
//        $filename = '';
//        if (isset($_FILES['insert-logo']['name']) && $_FILES['insert-logo']['name'] != '') {
//            try {
//                /* Starting upload */
//                $uploader = new Varien_File_Uploader('insert-logo');
//
//                // Any extention would work
//                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
//                $uploader->setAllowRenameFiles(false);
//
//                // Set the file upload mode 
//                // false -> get the file directly in the specified folder
//                // true -> get the file in the product like folders 
//                //    (file.jpg will go in something like /media/f/i/file.jpg)
//                $uploader->setFilesDispersion(true);
//
//                // We set media as the upload dir
//                $path = Mage::getBaseDir('media') . DS . 'magestore' . DS . 'pdfinvoiceplus'.DS.'logo';
//                $result = $uploader->save($path, $_FILES['insert-logo']['name']);
//                $filename = $result['file'];
//                
//                $logo = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/logo/'.$filename;
//                Mage::getModel('pdfinvoiceplus/template')->load($this->getRequest()->getParam('id'))
//                    ->setCompanyLogo($filename)
//                    ->save();
//                $this->getResponse()->setBody('<img width="160" src="'.$logo.'" />');
//            } catch (Exception $e) {
//                $filename = $_FILES['insert-logo']['name'];
//            }
//        }
//    }
//    
//    public function changebackgroundAction(){
//        $filename = '';
//        if (isset($_FILES['change-background']['name']) && $_FILES['change-background']['name'] != '') {
//            try {
//                /* Starting upload */
//                $uploader = new Varien_File_Uploader('change-background');
//
//                // Any extention would work
//                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
//                $uploader->setAllowRenameFiles(false);
//
//                // Set the file upload mode 
//                // false -> get the file directly in the specified folder
//                // true -> get the file in the product like folders 
//                //    (file.jpg will go in something like /media/f/i/file.jpg)
//                $uploader->setFilesDispersion(false);
//
//                // We set media as the upload dir
//                $path = Mage::getBaseDir('media') . DS . 'magestore' . DS . 'pdfinvoiceplus'.DS.'background';
//                $result = $uploader->save($path, $_FILES['change-background']['name']);
//                $filename = $result['file'];
//                $this->getResponse()->setBody(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/background/'.$filename);
//            } catch (Exception $e) {
//                $filename = $_FILES['change-background']['name'];
//            }
//        }
//    }
}
