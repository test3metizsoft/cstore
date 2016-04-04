<?php

class Magestore_Pdfinvoiceplus_Block_Sales_Order_Invoice_Items extends Mage_Sales_Block_Order_Invoice_Items {

    public function getCustomPrintUrl($invoice)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('pdfinvoiceplus/invoice/print', array('invoice_id' => $invoice->getId()));
        }
        return $this->getUrl('pdfinvoiceplus/invoice/print', array('invoice_id' => $invoice->getId()));
    }
}

?>
