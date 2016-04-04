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
 * Pdfinvoiceplus Helper
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function processAllVars($varialbles = array())
    {
        /* value and label */
        $varData = array();
        foreach ($varialbles as $variable)
        {
            $allKeysLabel = array();
            $allKeys = array();
            $allVars = array();
            /* Change by Zeus 03/12 */
            foreach (array_keys($variable) as $v)
            {
                if(isset($variable[$v]['label']) && isset($variable[$v]['value']))
                $allKeysLabel['label_' . $v] = $variable[$v]['label'] . ' ' . $variable[$v]['value'];
                if(isset($variable[$v]['value']))
                $allKeys[$v] = $variable[$v]['value'];
            }
            /* End change */
            $allVars = array_merge($allKeysLabel, $allKeys);
            $varData[] = $allVars;
        }
        foreach ($varData as $value)
        {
            foreach ($value as $key => $val)
            {
                $varsData[$key] = $val;
            }
        }
        return $varsData;
    }
    
     public function getAsVariable($varialble = array())
    {
        $data = array();

        foreach ($varialble as $data)
        {
            if (isset($data['label']) && isset($data['amount']))
            {
                $theData[$data['variable']] = $data['label'] . ' ' . $data['amount'];
            }
            if (isset($data['label']) && !isset($data['amount']))
            {
                $theData[$data['variable']] = $data['label'];
            }
            if (!isset($data['label']) && isset($data['amount']))
            {
                $theData[$data['variable']] = $data['amount'];
            }
        }
        return $theData;
    }

    /**
     * 
     * @param string $templateText
     * @return Core_Model_Email_Template Object 
     */
    public function setTheTemplateLayout($templateText)
    {
        $pdfProcessTemplate = Mage::getModel('core/email_template');
        $templateText = preg_replace('#\{\*.*\*\}#suU', '', $templateText);
        $pdfProcessTemplate->setTemplateText($templateText);

        return $pdfProcessTemplate;
    }
    
    public function arrayToStandard($variable = array())
    {
        foreach ($variable as $key => $var)
        {
            $variables[] = array($key => $var); 
        }
        return $variables;
    }
    
    public function checkEnable(){
        $config = Mage::getStoreConfig('pdfinvoiceplus/general/enable');
        return $config;
    }
    
     public function checkStoreTemplate() {
        $order = Mage::helper('pdfinvoiceplus/pdf')->getOrder();
        $collection = Mage::getModel('pdfinvoiceplus/template')->getCollection()
            ->addFieldToFilter('status', 1)
        ;
        if (Mage::helper('pdfinvoiceplus')->useMultistore()) {
            $collection->addFieldToFilter('stores', array('finset' => $order->getStoreId()));
            if ($collection->getSize() == 0) {
                $collection = Mage::getModel('pdfinvoiceplus/template')->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('stores', array('finset' => 0));
            }
        }

        if ($collection->getSize()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function useMultistore(){
        $store = Mage::app()->getStore()->getId();
        $config = Mage::getStoreConfig('pdfinvoiceplus/general/use_multistore', $store);
        if($config)
            return true;
        return false;
    }
        
    public function splitString($str, $length){
        $array = str_split($str, $length);
        return $array;
    }
    
    public function getImageViewUrl(){
        $model = Mage::registry('pdfinvoiceplus_data');
        if($model->getId()){
            $systemtemplate = Mage::getModel('pdfinvoiceplus/systemtemplate')->load($model->getSystemTemplateId());
            $url = Mage::getBaseUrl('media').'magestore/pdfinvoiceplus/'. $systemtemplate->getImage();
            return $url;
        }
        else{
            return '';
        }
    }
    
    public function getTemplateName(){
        $model = Mage::registry('pdfinvoiceplus_data');
        if($model->getId()){
            $systemtemplate = Mage::getModel('pdfinvoiceplus/systemtemplate')->load($model->getSystemTemplateId());
            return $systemtemplate->getTemplateName();
        }
        else{
            return '';
        }
    }
    
    /**
     * check template are using is A6, A7 or no
     * @return boolean
     */
    public function isSizeA6OrA7(){
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        if($template->getFormat() == 'A6' || $template->getFormat() == 'A7')
            return true;
        return false;
    }
    /**
     * update all template from old versions to 2.0
     */
    public function updateTemplate(){
        $templates = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        foreach($templates as $template){
           $blockSelect = Mage::app()->getLayout()->createBlock('pdfinvoiceplus/adminhtml_design_loadtemplate');
           $localization = 'default';
           if($template->getLocalization())
               $localization = $template->getLocalization();
           $blockSelect->setLocale($localization)->setDataObject($template->getData());
           $templateCode = Mage::getModel('pdfinvoiceplus/systemtemplate')
                               ->load($template->getSystemTemplateId())->getTemplateCode();
           $blockSelect->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/order.phtml');
           $template->setData('order_html',$blockSelect->toHtml());
           $blockSelect->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/invoice.phtml');
           $template->setData('invoice_html',$blockSelect->toHtml());
           $blockSelect->setTemplate('pdfinvoiceplus/loadtemplate/' . $templateCode . '/creditmemo.phtml');
           $template->setData('creditmemo_html',$blockSelect->toHtml());
           $template->save();
        }
    }
     public function isRemovePrintDefault(){
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig('pdfinvoiceplus/general/remove_print_default',$storeId);
    }
}