<?php
class SM_XPos_Block_Adminhtml_Catalog_Product_Edit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sm/xpos/index/catalog/product/edit.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    public function getHeader()
    {
        $header = Mage::helper('catalog')->__('New Product');

        if ($setName = $this->getAttributeSetName()) {
            $header.= ' (' . $setName . ')';
        }
        return $header;
    }

    public function getSet()
    {
        return $this->getRequest()->getParam('set', null);
    }

}
