<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Masspdforder extends Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator
{
//    private $_invoiceId = null;
//    public function setInvoiceId($id){
//        $this->_invoiceId = $id;
//    }
     public function getPdfDataOrder($orderIds)
    {
         if (isset($orderIds))
        {
            $pdf = $this->loadPdf();

            foreach ($orderIds as $orderId)
            {
                $order = Mage::getModel('sales/order')->load($orderId);
                 if($order->getId())
                 Mage::register('current_order', $order);
//                $pdfData = Mage::getBlockSingleton('pdfinvoiceplus/adminhtml_pdf')->getOrderPdf();
                $pdfData = Mage::getModel('pdfinvoiceplus/entity_orderpdf')->getThePdf((int) $orderId);
                $pagebreak = '<pagebreak>';
                if ($orderId === end($orderIds))
                {
                    $pagebreak = '';
                }
                $pdf->WriteHTML($pdfData->getData('htmltemplate') . $pagebreak);
                Mage::unregister('current_order');
            }
            
        }
        
        
        return $pdf->Output('', 'S');
    }
    
}
