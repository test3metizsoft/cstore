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
 * @package     Magestore_Invoiceeditor
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Invoiceeditor Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Invoiceeditor
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Sales_Order_Invoice_View extends Mage_Adminhtml_Block_Sales_Order_Invoice_View
{
    /*
     * The constructor to get the template
     */

    public function __construct()
    {
        parent::__construct();
        $enable = Mage::helper('pdfinvoiceplus')->checkEnable();
        $active = Mage::helper('pdfinvoiceplus')->checkStoreTemplate();
        if($enable == 1 && $active == 1){
            $this->_addButton('printpdf', array(
                'label' => Mage::helper('pdfinvoiceplus')->__('Print Custom PDF File'),
                'class' => 'save',
                'onclick' => $this->getPrintPdfLink()
                    )
            );
        }
    }
    
    private function getPrintPdfLink()
    {
        $templateCollection = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        $templateCollection->addFieldToSelect('*')
                //->addFieldToFilter('store_id', $this->getCurrentInvoiceOrderStore())
                ->addFieldToFilter('is_active', 1);

        $templateId = $templateCollection->getFirstItem()->getId();

        if (!empty($templateId))
        {
            return $location = 'setLocation(\'' . $this->getCustomPrintUrl() . '\')';
        }
        $messege = Mage::helper('pdfinvoiceplus')->__('You do not have a template selected for this invoice. You will get the default Magento Invoice');
        //return $location = "confirmSetLocation('{$messege}', '{$this->getPrintUrl()}')";
        return $location = 'setLocation(\'' . $this->getCustomPrintUrl() . '\')';
    }
    
    public function getCustomPrintUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/adminhtml_invoice/print', array(
                    'invoice_id' => $this->getInvoice()->getId()
                ));
    }
    
    private function getCurrentInvoiceOrderStore()
    {
        if ($storeId = $this->getInvoice()->getOrder()->getStore()->getId())
        {
            return array(0, $storeId);
        }
        return array(0);
    }
}