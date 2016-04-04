<?php
class Magestore_Pdfinvoiceplus_Model_Template01_Entity_Order extends Magestore_Pdfinvoiceplus_Model_Entity_Abstract
{
    private $_orderId = null;
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdfinvoiceplus/entity_order');
    }
    public function setOrderId($id){
        $this->_orderId = $id;
    }
    
    public function getTemplateBody(){
        return 'body';
    }
    public function getHeader(){
        return 'header';
    }
    public function getFooter(){
        return 'footer';
    }
    public function getCss(){
        return 'css';
    }
    
    public function getInstanceSource() {
        $orderId = $this->_orderId;
        return Mage::getModel('sales/order')->load($orderId);
    }
    
    public function getPdf($html) {
        return parent::getPdf($html);
    }

    
}
?>
