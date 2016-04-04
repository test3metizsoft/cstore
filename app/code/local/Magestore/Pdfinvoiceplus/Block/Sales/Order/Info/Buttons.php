<?php

class Magestore_Pdfinvoiceplus_Block_Sales_Order_Info_Buttons extends Mage_Sales_Block_Order_Info_Buttons {

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pdfinvoiceplus/sales/order/info/buttons.phtml');
    }
    
    public function getCustomPrintUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('pdfinvoiceplus/order/print', array('order_id' => $order->getId()));
        }
        return $this->getUrl('pdfinvoiceplus/order/print', array('order_id' => $order->getId()));
    }
}

?>
