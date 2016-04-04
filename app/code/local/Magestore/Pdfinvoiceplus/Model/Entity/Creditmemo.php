<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Creditmemo extends Magestore_Pdfinvoiceplus_Model_Entity_Abstract
{
    private $_creditmemoId = null;
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdfinvoiceplus/entity_creditmemo');
    }
    public function setCreditmemoId($id){
        $this->_creditmemoId = $id;
    }
    
    public function getInstanceSource(){
        $creditmemo = $this->_creditmemoId;
        return Mage::getModel('sales/order_creditmemo')->load($creditmemo);
    }
   
    
    public function getFooter(){
        return 'footer';
    }
    public function getCss(){
        return 'css';
    }
    public function getPdf($html) {
        return parent::getPdf($html);
    }
}

?>