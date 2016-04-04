<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Masspdfcreditmemo extends Magestore_Pdfinvoiceplus_Model_Entity_Creditmemogenerator
{   
    public function getPdfDataCreditmemos($creditmemoIds){
        if(isset($creditmemoIds)){
            $pdf = $this->loadPdf();
            foreach ($creditmemoIds as $creditmemoId){
                $creditmemo = Mage::getSingleton('sales/order_creditmemo')->load($creditmemoId);
                if($creditmemo->getId())
                    Mage::register('current_creditmemo',$creditmemo);
//                $pdfData = Mage::getBlockSingleton('pdfinvoiceplus/adminhtml_pdf')->getCreditmemoPdf();
                $pdfData = Mage::getModel('pdfinvoiceplus/entity_creditmemopdf')->getThePdf((int) $creditmemoId);
                $pagebreak = '<pagebreak>';
                if ($creditmemoId === end($creditmemoIds))
                {
                    $pagebreak = '';
                }
                $pdf->WriteHTML($pdfData->getData('htmltemplate') . $pagebreak);
                Mage::unregister('current_creditmemo');
            }
        }
         return $pdf->Output('', 'S');
        
    }

}

?>