<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Totals_Subtotal extends Mage_Core_Model_Abstract
{

    /**
     * Get the variables and values acording to tax rules
     * @return array
     */
    public function getTotalsForDisplay()
    {
        if(Mage::app()->getRequest()->getParam('order_id')){
            $store = $this->getSource()->getStore();
        }else{
            $store = $this->getSource()->getOrder()->getStore();
        }
        $helper = Mage::helper('tax');
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getSource()->getSubtotalInclTax())
        {
            $amountInclTax = $this->getSource()->getSubtotalInclTax();
        } else
        {
            $amountInclTax = $this->getAmount()
                    + $this->getSource()->getTaxAmount()
                    - $this->getSource()->getShippingTaxAmount();
        }

        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);


            $totals = array(array(
                'subtotalexcludingtax' => array(
                    'value' => $this->getAmountPrefix() . $amount,
                    'label' => Mage::helper('tax')->__('Subtotal (Excl. Tax)') . ':',
                ),
                'subtotalincludingtax' => array(
                    'value' => $this->getAmountPrefix() . $amountInclTax,
                    'label' => Mage::helper('tax')->__('Subtotal (Incl. Tax)') . ':',
                ),
            ));


        return $totals;
    }

    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod('subtotal');
    }

}

?>