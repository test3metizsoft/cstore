<?php
/**
 * Created by PhpStorm.
 * User: Smartor
 * Date: 10/23/14
 * Time: 2:16 PM
 */
class SM_XPos_Block_Report_Eodreport_Eodreport_Render_Warehouse extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row) {
        if(Mage::helper('xpos/configXPOS')->getIntegrateXmwhEnable() == 1){
            $warehouseid = $row->getData('warehouse_id');
            if (!empty($warehouseid)) {
                $user = Mage::getModel('xwarehouse/warehouse')->load($warehouseid);
                return $user->getData('label');
            }
            else{
                return 'NO Warehouse';
            }
        }
        else{
            return 'NO Warehouse';
        }
    }
}
