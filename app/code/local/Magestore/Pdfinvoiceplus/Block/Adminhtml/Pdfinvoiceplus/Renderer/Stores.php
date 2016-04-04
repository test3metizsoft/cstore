<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Renderer_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $storeString = $row->getStores();
        $stores = explode(',', $storeString);
        if (in_array('0', $stores)) {
            $show = 'All Store Views';
        } else {
            $storeNames = array();
            foreach ($stores as $storeId) {
                $storeName = Mage::getModel('core/store')->load($storeId)->getName();
                $storeNames[] = $storeName;
            }
            $show = implode(', ', $storeNames);
        }
        return $show;
    }

}

?>
