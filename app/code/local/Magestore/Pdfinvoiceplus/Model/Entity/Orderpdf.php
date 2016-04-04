<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Orderpdf extends Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator {

    public $orderId;
    public $templateId;
    public $order = null;

    public function getTheOrder() {
        if (is_null($this->order)) {
            $order = Mage::getModel('sales/order')->load($this->orderId);
            return $order;
        }
        return $this->order;
    }

    public function setTheOrder($order) {
        $this->order = $order;
    }

    public function getThePdf($orderId, $templateId = NULL) {
        $this->templateId = $templateId;
        $this->orderId = $orderId;
        $this->setVars(Mage::helper('pdfinvoiceplus')->processAllVars($this->collectVars()));
        /* Change by Zeus 04/12 */
        $html = NULL;
        return $this->getPdf($html);
        /* end change */
    }

    public function collectVars() {
        $vars = Mage::getModel('pdfinvoiceplus/entity_additional_info')
            ->setSource($this->getTheOrder())
            ->setOrder($this->getTheOrder())
            ->getTheInfoMergedVariables();
        return $vars;
    }

}
