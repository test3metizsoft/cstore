<?php



class Magestore_Pdfinvoiceplus_Model_Entity_Totals_Grandtotal extends Mage_Core_Model_Abstract
{
    
    public function getTotalsForDisplay()
    {
        if(Mage::app()->getRequest()->getParam('order_id')){
            $store = $this->getSource()->getStore();
        }else{
            $store = $this->getSource()->getOrder()->getStore();
        }
        $config = Mage::getSingleton('tax/config');

        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountExclTax = $this->getAmount() - $this->getSource()->getTaxAmount();
        $amountExclTax = ($amountExclTax > 0) ? $amountExclTax : 0;
        $amountExclTax = $this->getOrder()->formatPriceTxt($amountExclTax);
        $tax = $this->getOrder()->formatPriceTxt($this->getSource()->getTaxAmount());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        $totals = array(array(
            'grandtotalexcludingtax' => array(
                    'value' => $this->getAmountPrefix() . $amountExclTax,
                    'label' => Mage::helper('tax')->__('Grand Total (Excl. Tax)') . ':',
            )));
        $totals[] = array(
            'grandtotaltax' => array(
                'value' => $this->getAmountPrefix() . $tax,
                'label' => Mage::helper('tax')->__('Tax') . ':',
                ));
        $totals[] = array(
            'grandtotalincludingtax' => array(
                'value' => $this->getAmountPrefix() . $amount,
                'label' => Mage::helper('tax')->__('Grand Total (Incl. Tax)') . ':',
                ));
        return $totals;
    }


    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('grand_total');
    }
}
?>
