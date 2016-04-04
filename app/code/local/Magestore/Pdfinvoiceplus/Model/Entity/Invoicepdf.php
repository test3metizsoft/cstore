<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Invoicepdf extends Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator {

    public $invoiceId;
    public $templateId;

    public function getTheInvoice() {
        $invoice = Mage::getModel('sales/order_invoice')->load($this->invoiceId);
        return $invoice;
    }

    public function getThePdf($invoiceId, $templateId = NULL) {
        $this->templateId = $templateId;
        $this->invoiceId = $invoiceId;
        $this->setVars(Mage::helper('pdfinvoiceplus')->processAllVars($this->collectVars()));
        /*Change by Zeus 04/12*/
        $html = NULL;
        return $this->getPdf($html);
        /* end change */
    }

    public function collectVars() {
        /* Change By Jack 25/12 */
        $vars = Mage::getModel('pdfinvoiceplus/entity_additional_info')
            ->setSource($this->getTheInvoice())
            ->setInvoice($this->getTheInvoice())    
            ->setOrder($this->getTheInvoice()->getOrder())
            ->getTheInfoMergedVariables();
        return $vars;
        /* End Change */
    }

}

