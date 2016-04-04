<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {
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
        return $this->getUrl('pdfinvoiceplusadmin/adminhtml_order/print', array(
                    'order_id' => $this->getOrder()->getId()
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

?>
