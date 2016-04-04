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
 * Pdfinvoiceplus Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'pdfinvoiceplus';
        $this->_controller = 'adminhtml_pdfinvoiceplus';

        $this->_updateButton('save', 'label', Mage::helper('pdfinvoiceplus')->__('Save Template'));
        $this->_updateButton('save', 'onclick', 'saveTemplate()');

        $this->_updateButton('delete', 'label', Mage::helper('pdfinvoiceplus')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
            ), -100);

        $previewUrl = Mage::helper('adminhtml')->getUrl('pdfinvoiceplusadmin/adminhtml_pdfinvoiceplus/printinvoice');
        $urlTinyBox = Mage::helper('adminhtml')->getUrl('pdfinvoiceplusadmin/adminhtml_pdfinvoiceplus/showTemplate');

        $usingConfirmSave = $this->getRequest()->getParam('id') ? '1' : '0';
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pdfinvoiceplus_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'pdfinvoiceplus_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'pdfinvoiceplus_content');
            }
            
            function checkSave(){
                if(!PdfInvoiceTemplate.isSelect && $('system_template_id').value == ''){
                    alert(\"" . Mage::helper('pdfinvoiceplus')->__("Can\'t save. You must select design first!") . "\");
                    return false;
                }
                return true;
            }
            
            function saveTemplate(){
                if(!checkSave()){
                    return false;
                }
                editForm.submit();
            }

            function saveAndContinueEdit(){
                if(!checkSave()){
                    return false;
                }
                if(PdfInvoiceTemplate.isSelect && " . $usingConfirmSave . "){
                    if(confirm('" . Mage::helper('pdfinvoiceplus')->__('You have selected a new design. Are you sure you want to change?') . "')){
                        
                    }else{
                        return false;
                        //PdfInvoiceTemplate.cancelSelect();
                    }
                }
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function previewTemplate(){
                editForm.submit('$previewUrl');
            }
            
            function checkingImg(template_id){
                var url = '" . $urlTinyBox . "template_id/' + template_id;
                TINY.box.show(url,1,800,900,1);
            }
            
            function disableBarcodeType()
            {
                if($('barcode').value == 2){
                    $('barcode_type').disabled = true;
                }else{
                    $('barcode_type').disabled = false;
                }
            }
            if($('barcode').value == 2){
                    $('barcode_type').disabled = true;
            }
            Event.observe($('barcode'),'change',disableBarcodeType);
        ";
    }

    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->addJs('magestore/pdfinvoiceplus/variables.js');
        $this->getLayout()->getBlock('head')->addJs('lib/flex.js');
        $this->getLayout()->getBlock('head')->addJs('lib/FABridge.js');
        $this->getLayout()->getBlock('head')->addJs('mage/adminhtml/flexuploader.js');
        $this->getLayout()->getBlock('head')->addJs('mage/adminhtml/browser.js');
        $this->getLayout()->getBlock('head')->addJs('extjs/ext-tree.js');
        $this->getLayout()->getBlock('head')->addJs('extjs/ext-tree-checkbox.js');

        $this->getLayout()->getBlock('head')->addItem('js_css', 'extjs/resources/css/ext-all.css');
        $this->getLayout()->getBlock('head')->addItem('js_css', 'extjs/resources/css/ytheme-magento.css');
        $this->getLayout()->getBlock('head')->addItem('js_css', 'prototype/windows/themes/default.css');
        $this->getLayout()->getBlock('head')->addJs('magestore/pdfinvoiceplus/window.js')
            ->addItem('js_css', 'magestore/pdfinvoiceplus/magento.css');
        return parent ::_prepareLayout();
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('pdfinvoiceplus_data') && Mage::registry('pdfinvoiceplus_data')->getId()
        ) {
            return Mage::helper('pdfinvoiceplus')->__("Edit Template '%s'", $this->htmlEscape(Mage::registry('pdfinvoiceplus_data')->getTemplateName())
            );
        }
        return Mage::helper('pdfinvoiceplus')->__('Add Template');
    }

}
