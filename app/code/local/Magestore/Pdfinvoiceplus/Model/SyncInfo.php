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
 * Pdfinvoiceplus Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
/* Change by Zeus 04/12 */
if (!defined('DS')) {
    define( 'DS', DIRECTORY_SEPARATOR );
}
//define('DS', DIRECTORY_SEPARATOR);
/* end change */
define('PDFINVOICEPLUS_PATH_LOGO', 'magestore' . DS . 'pdfinvoiceplus' . DS . 'logo');

class Magestore_Pdfinvoiceplus_Model_SyncInfo {

    const PATH_LOGO = 'magestore/pdfinvoiceplus/logo';

    protected $_barcode_before;

    public function updateBarcodeBefore($observer) {
        $model = $observer['model'];
        $this->_barcode_before = $model->getBarcode();
        $this->_barcode_type_before = $model->getBarcodeType();
        $this->_barcode_order_before = $model->getBarcodeOrder();
        $this->_barcode_invoice_before = $model->getBarcodeInvoice();
        $this->_barcode_creditmemo_before = $model->getBarcodeCreditmemo();
        return $this;
    }

    public function updateBarcode($observer) {
        $model = $observer['model'];
        if ($this->_barcode_before != $model->getBarcode() ||
            $this->_barcode_type_before != $model->getBarcodeType() ||
            $this->_barcode_order_before != $model->getBarcodeOrder() ||
            $this->_barcode_invoice_before != $model->getBarcodeInvoice() ||
            $this->_barcode_creditmemo_before != $model->getBarcodeCreditmemo()
        ) { //if is update
            $barcode_type = $model->getBarcodeType();
            $barcode_order = $model->getBarcodeOrder();
            $barcode_invoice = $model->getBarcodeInvoice();
            $barcode_creditmemo = $model->getBarcodeCreditmemo();

            $order_html = $model->getOrderHtml();
            $invoice_html = $model->getInvoiceHtml();
            $creditmemo_html = $model->getCreditmemoHtml();

            //define dom
            $order_html_dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
            $invoice_html_dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
            $creditmemo_html_dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;

            //init dom html
            if ($order_html != '') {
                $order_html_dom->load($order_html);
            }
            if ($invoice_html != '') {
                $invoice_html_dom->load($invoice_html);
            }
            if ($creditmemo_html != '') {
                $creditmemo_html_dom->load($creditmemo_html);
            }
            if ($model->getBarcode() == '1') {
                try {
                    //update barcode for order
                    if ($order_html_dom->root->innertext) {
                        $barcodeBoxOrder = $order_html_dom->find('.barcode', 0);
                        if ($barcodeBoxOrder->find('barcode', 0)) {
                            $barTag = $barcodeBoxOrder->find('barcode', 0);
                            $barTag->code = $barcode_order;
                            $barTag->type = $barcode_type;
                        } else {
                            $barcodeBoxOrder->innertext = '<barcode code="' . $barcode_order . '" type="' . $barcode_type . '" />';
                        }
                        $model->setOrderHtml($order_html_dom->save());
                    }

                    //update barcode for invoice
                    if ($invoice_html_dom->root->innertext) {
                        $barcodeBoxInvoice = $invoice_html_dom->find('.barcode', 0);
                        if ($barcodeBoxInvoice->find('barcode', 0)) {
                            $barTag = $barcodeBoxInvoice->find('barcode', 0);
                            $barTag->code = $barcode_invoice;
                            $barTag->type = $barcode_type;
                        } else {
                            $barcodeBoxInvoice->innertext = '<barcode code="' . $barcode_invoice . '" type="' . $barcode_type . '" />';
                        }
                        $model->setInvoiceHtml($invoice_html_dom->save());
                    }

                    if ($creditmemo_html_dom->root->innertext) {
                        //update barcode for Creditmemo
                        $barcodeBoxCreditmemo = $creditmemo_html_dom->find('.barcode', 0);
                        if ($barcodeBoxCreditmemo->find('barcode', 0)) {
                            $barTag = $barcodeBoxCreditmemo->find('barcode', 0);
                            $barTag->code = $barcode_creditmemo;
                            $barTag->type = $barcode_type;
                        } else {
                            $barcodeBoxCreditmemo->innertext = '<barcode code="' . $barcode_creditmemo . '" type="' . $barcode_type . '" />';
                        }
                        $model->setCreditmemoHtml($creditmemo_html_dom->save());
                    }


                    $model->save();
                } catch (Exception $e) {
                    
                }
            } else {

                //update barcode for order
                if ($order_html_dom->root->innertext) {
                    $order_html_dom->find('.barcode', 0)->innertext = '';
                    $model->setOrderHtml($order_html_dom->save());
                }

                //update barcode for invoice
                if ($invoice_html_dom->root->innertext) {
                    $invoice_html_dom->find('.barcode', 0)->innertext = '';
                    $model->setInvoiceHtml($invoice_html_dom->save());
                }

                //update barcode for Creditmemo
                if ($creditmemo_html_dom->root->innertext) {
                    $creditmemo_html_dom->find('.barcode', 0)->innertext = '';
                    $model->setCreditmemoHtml($creditmemo_html_dom->save());
                }
                $model->save();
            }
        }
        return $this;
    }

    /**
     * this function sync infomation to order_html, invoice_html, creditmemo_html
     * after event save had saved in database
     * 
     * @param type $observer
     * @return Magestore_Pdfinvoiceplus_Model_Observer
     */
    public function syncInfo($observer) {
        //$action = $observer->getEvent()->getControllerAction();
        $data = $observer['data'];
        $id = $observer['id'];
        $this->syncExecution($data, $id);

        return $this;
    }

    public function syncExecution($id, $type) {
        $model = Mage::getModel('pdfinvoiceplus/template')->load($id);

        if (!$model->getId()) {
            return;
        }
        
        //datas
        $data = array(
            'company_logo' => $model->getCompanyLogo(),
            'company_name' => $model->getCompanyName(),
            'company_address' => $model->getCompanyAddress(),
            'company_email' => $model->getCompanyEmail(),
            'company_telephone' => $model->getCompanyTelephone(),
            'company_fax' => $model->getCompanyFax(),
            'business_id' => $model->getBusinessId(),
            'vat_number' => $model->getVatNumber(),
            'vat_office' => $model->getVatOffice(),
            'note'       => $model->getNote(),
            'footer'     => $model->getFooter(),
            'footer_height' => $model->getFooterHeight(),
            'terms_conditions'  => $model->getTermsConditions()
        );
        
        //init dom html
        $html_dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        switch ($type){
            case 'order':
                $html_dom->load($model->getOrderHtml());
                $outHtml = $this->renderHtml($html_dom, $data);
                if($outHtml){
                    $data['order_html'] = $outHtml;
                }
                break;
            case 'invoice':
                $html_dom->load($model->getInvoiceHtml());
                $outHtml = $this->renderHtml($html_dom, $data);
                if($outHtml){
                    $data['invoice_html'] = $outHtml;
                }
                break;
            case 'creditmemo':
                $html_dom->load($model->getCreditmemoHtml());
                $outHtml = $this->renderHtml($html_dom, $data);
                if($outHtml){
                    $data['creditmemo_html'] = $outHtml;
                }
                break;
            default:
                return;
                //break;
        }
        
        /********************************* */
        //save data//
        $model->addData($data)
            ->setId($id);
        try {
            $model->save();
        } catch (Exception $e) {
            echo $e->getMessage() . ' ' . PHP_EOL;
        }

        return $this;
    }
    /*********************************** */
    // reset template default
    public function resetTemplate($id,$type){
        $model = Mage::getModel('pdfinvoiceplus/template')->load($id);
        $templateCode = Mage::getModel('pdfinvoiceplus/systemtemplate')
                        ->load($model['system_template_id'])->getTemplateCode();
        $blockSelect = Mage::app()->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_loadtemplate');
        $blockSelect->setLocale($model['localization'])->setDataObject($model->getData());
        if(!$model->getId()){
            return;
        }
        /* Change By Jack 03/12 */
        switch ($type){
            case 'order':
                $blockSelect->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/order.phtml');
                $data["order_html"] = $blockSelect->toHtml();
                break;
            case 'invoice':
                $blockSelect->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/invoice.phtml');
                $data["invoice_html"] = $blockSelect->toHtml();
                break;
            case 'creditmemo':
                $blockSelect->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/creditmemo.phtml');
                $data["creditmemo_html"] = $blockSelect->toHtml();
                break;
            default:
                return;
        }
        /* End Change */
        $model->addData($data)
            ->setId($id);
        try {
            $model->save();
        } catch (Exception $e) {
            echo $e->getMessage() . ' ' . PHP_EOL;
        }

        return $this;
    }

    public function getDataNameInfo() {
        $_helper = Mage::helper('pdfinvoiceplus');
        return array(
            //'company_logo' => '',
            'company_name' => '',
            'company_address' => $_helper->__('Address:'),
            'company_email' => $_helper->__('Email:'),
            'company_telephone' => $_helper->__('Tel:'),
            'company_fax' => $_helper->__('Fax:'),
            'business_id' => $_helper->__('Business ID:'),
            'vat_number' => $_helper->__('VAT Number:'),
            'vat_office' => $_helper->__('VAT Office:'),
            //'note'              => $_helper->__(''),
            'footer' => $_helper->__('Footer'),
            //'terms_conditions'  => $_helper->__('')
        );
    }

    protected function renderHtml($html_dom, $data) {
        if(!$html_dom->root->innertext){
            return false;
        }
        //replace image logo
        foreach ($html_dom->find("[info-img]") as $element) {
            $data_name = $element->attr['info-img'];
            if (isset($data[$data_name])) {
                foreach ($html_dom->find('[info-img="' . $data_name . '"]') as $e) {
                    if ($data[$data_name]) {
                        $e->innertext = '<img width="160" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::PATH_LOGO . '/' . $data[$data_name] . '"/>';
                    } else {
                        $e->innertext = '';
                    }
                }
            }
        }
        //replace info-box //update all info-text=
        $info_vars = $this->getDataNameInfo();
        foreach ($html_dom->find("[info-text]") as $element) {
            $data_name = $element->attr['info-text'];
            if (isset($data[$data_name])) {
                foreach ($html_dom->find('[info-text="' . $data_name . '"]') as $e) {
                    $e->innertext = $data[$data_name];
                }
                unset($info_vars[$data_name]);
            }
        }
        //anny not found info then add info-box
        $box_info = $html_dom->find(".box-infomations", 0);
        $inner_html = $box_info->innertext;
        foreach ($info_vars as $data_name => $label) {
            if ($data[$data_name]) {
                $html_element = '<p class="info-' . $data_name . ' general-info color-text" style="pading: 0; margin: 0;"><span class="info-label">' . $label . ' </span><span class="info-value" info-text="' . $data_name . '">' . $data[$data_name] . '</span></p>';
                $inner_html .= $html_element;
            }
        }
        $box_info->innertext = $inner_html;
        //if not found info box then add single info
        foreach ($info_vars as $data_name => $label) {
            if ($data[$data_name]) {
                $find = $html_dom->find('[info-text-outer=' . $data_name . ']', 0);
                $find->innertext = '<p class="info-' . $data_name . ' general-info color-text" style="pading: 0; margin: 0;"><span class="info-label">' . $label . ' </span><span class="info-value" info-text="' . $data_name . '">' . $data[$data_name] . '</span></p>';
            }
        }

        //not found note then add note to .note-box
        if ($data['note'] && !$html_dom->find('[info-text=note]', 0)->innertext) {
            $html_dom->find('.note-box', 0)->innertext = '<div contextmenu-type="main" contenteditable="true" class="contenteditable color-text note p-note"><strong>Note: </strong><span class="info-value" info-text="note">' . $data['note'] . '</span></div>';
        }
        //not found note then add note to .term-conditions-box
        if ($data['terms_conditions'] && !$html_dom->find('[info-text=terms_conditions]', 0)->innertext) {
            if (!$html_dom->find('.term-conditions-box', 0)->innertext) {
                $html_dom->find('.term-conditions', 0)->innertext = '<div contextmenu-type="main" contenteditable="true" class="contenteditable color-text term p-note"><strong>Term & conditions: </strong><span class="info-value" info-text="terms_conditions">' . $data['terms_conditions'] . '</span></div>';
            } else {
                $html_dom->find('.term-conditions-box', 0)->innertext = '<div contextmenu-type="main" contenteditable="true" class="contenteditable color-text term p-note"><strong>Term & conditions: </strong><span class="info-value" info-text="terms_conditions">' . $data['terms_conditions'] . '</span></div>';
            }
        }
        
        //footer height
        if(isset($data['footer_height']) && $data['footer_height']){
            foreach ($html_dom->find("[id=footer]") as $element) {
                $element->setAttribute('style', 'height:'.$data['footer_height'].'px');
            }
            foreach ($html_dom->find("[id=container-inner]") as $element) {
                $element->setAttribute('style', 'padding-bottom:'.($data['footer_height']+10).'px');
            }
        }

        return $html_dom->save();
    }

}
