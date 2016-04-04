<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Masspdfinvoice extends Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator
{
     public function getPdfDataInvoice($invoiceIds)
    {
         if (isset($invoiceIds))
        {
            $pdf = $this->loadPdf();

            foreach ($invoiceIds as $invoiceId)
            {
                $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
                 if($invoice->getId())
                 Mage::register('current_invoice', $invoice);
//                $pdfData = Mage::getBlockSingleton('pdfinvoiceplus/adminhtml_pdf')->getInvoicePdf((int) $invoiceId);
                $pdfData = Mage::getModel('pdfinvoiceplus/entity_invoicepdf')->getThePdf((int) $invoiceId);
                $pagebreak = '<pagebreak>';
                if ($invoiceId === end($invoiceIds))
                {
                    $pagebreak = '';
                }
                $pdf->WriteHTML($pdfData->getData('htmltemplate') . $pagebreak);
                Mage::unregister('current_invoice');
            }
            
        }
        
        
        return $pdf->Output('', 'S');
    }
}

?>