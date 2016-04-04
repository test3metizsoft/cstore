<?php
class Magestore_Pdfinvoiceplus_Model_Mysql4_Template extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('pdfinvoiceplus/template', 'template_id');
    }
}
?>
