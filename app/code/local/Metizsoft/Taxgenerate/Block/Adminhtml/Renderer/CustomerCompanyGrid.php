<?php

class Metizsoft_Taxgenerate_Block_Adminhtml_Renderer_CustomerCompanyGrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $address = Mage::getModel('customer/customer')->load($row->getId());
        $company = '';
        foreach ($address->getAddresses() as $address) {
            $company .= $address->getCompany().', ';
        }
        return $company;
    }
}
