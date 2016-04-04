<?php

abstract class Magestore_Pdfinvoiceplus_Model_Entity_Abstract extends Mage_Core_Model_Abstract {

    abstract public function getCss();

    //abstract public function getFileName();
    abstract public function getInstanceSource();

    public function getFileName() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        if(Mage::app()->getRequest()->getParam('invoice_id')){
            $filename = $template->getInvoiceFilename();
        }elseif(Mage::app()->getRequest()->getParam('creditmemo_id')){
            $filename = $template->getCreditmemoFilename();
        }else{
            $filename = $template->getOrderFilename();
        }
        $filter = Mage::getModel('cms/template_filter');
        $vars = Mage::helper('pdfinvoiceplus')->processAllVars(Mage::helper('pdfinvoiceplus/pdf')->collectVars());
        $filter->setVariables($vars);
        $filename = $filter->filter($filename);
        return $filename;
    }

    /**
     * get system template in use
     * @return system template
     */
    public function getSystemTemplate() {
        $activeTemplate = Mage::getModel('pdfinvoiceplus/template')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
        $systemTemplate = Mage::getModel('pdfinvoiceplus/systemtemplate')
            ->load($activeTemplate->getSystemTemplateId());
        return $systemTemplate;
    }

    public function getOrientation() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $orientation = $template->getOrientation();
        if ($orientation == 1)
            return 'L';
        return 'P';
    }

    public function getPdf($html) {
        $mailPdf = new Varien_Object;
        $pdf = $this->loadPdf();
        $pdf->WriteHTML($this->getCss(), 1);
        $pdf->WriteHTML($html);

        $mailPdf->setData('htmltemplate', $html);
        $output = $pdf->Output($this->getFileName(), 'S');

        $mailPdf->setData('pdfbody', $output);
        $mailPdf->setData('filename', $this->getFileName());

        return $mailPdf;
    }

    public function pdfPaperFormat() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $format = $template->getFormat();
        $format = $format ? $format : 'A4';
        return $format;
    }

    public function loadPdf() {
        $top = '0';
        $bottom = '0';
        $left = '0';
        $right = '0';
        $orientation = $this->getOrientation();
        $pdf = new Mpdf_Magestorepdf('', $this->pdfPaperFormat(), 8, '', $left, $right, $top, $bottom);
        $pdf->AddPage($orientation);
        return $pdf;
    }
}

?>