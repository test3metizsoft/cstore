<?php
class SM_Xpos_Block_Adminhtml_Index_Till_Listtill extends Mage_Core_Block_Template{
    public function __construct(){

    }

    public function loadTill(){
        if(Mage::helper('xpos/configXPOS')->getIntegrateXmwhEnable()){
            $warehouse_id = Mage::getSingleton('adminhtml/session')->getWarehouseId();
            $collection = Mage::getModel('xpos/till')->getCollection()
                ->addFieldToFilter('warehouse_id',$warehouse_id)
                ->addFieldToFilter('is_active',1);
        }else{
            $collection = Mage::getModel('xpos/till')->getCollection()
                ->addFieldToFilter('is_active',1);
        }

        return $collection;
    }

}
