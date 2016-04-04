<?php

class Metizsoft_Taxgenerate_Block_Adminhtml_Renderer_OrderAddressGrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $street = $row->getData('street');
        $city = $row->getData('city');
        $statecounty = $row->getData('statecounty');
        $region = $row->getData('region');
        $postcode = $row->getData('postcode');
        return $street.', '.$city.', '.$statecounty.', '.$region.', '.$postcode;
    }
}
