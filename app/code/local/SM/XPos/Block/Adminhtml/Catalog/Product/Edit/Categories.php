<?php
class SM_XPos_Block_Adminhtml_Catalog_Product_Edit_Categories extends Mage_Core_Block_Template
{
    public function getCategoryId()
    {
        $rootCategory = Mage::app()->getWebsite(1)->getDefaultStore()->getRootCategoryId();
        return $this->getRequest()->getParam('category', $rootCategory);
    }
}
