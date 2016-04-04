<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Invoice extends Magestore_Pdfinvoiceplus_Model_Entity_Abstract
{
    private $_invoiceId = null;
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdfinvoiceplus/entity_invoice');
    }
    public function setInvoiceId($id){
        $this->_invoiceId = $id;
    }
    
    public function getInstanceSource(){
        $invoiceId = $this->_invoiceId;
        return Mage::getModel('sales/order_invoice')->load($invoiceId);
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