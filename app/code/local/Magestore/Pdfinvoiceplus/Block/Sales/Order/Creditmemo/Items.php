<?php

class Magestore_Pdfinvoiceplus_Block_Sales_Order_Creditmemo_Items extends Mage_Sales_Block_Order_Creditmemo_Items {


    public function getCustomPrintUrl($creditmemo)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('pdfinvoiceplus/creditmemo/print', array('creditmemo_id' => $creditmemo->getId()));
        }
        return $this->getUrl('pdfinvoiceplus/creditmemo/print', array('creditmemo_id' => $creditmemo->getId()));
    }
}

?>
