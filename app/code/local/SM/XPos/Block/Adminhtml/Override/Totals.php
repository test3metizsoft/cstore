<?php

class SM_XPos_Block_Adminhtml_Override_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Totals
{

    private $_isRecollectTotal = false;

    public function reCollectTotal($flag = false)
    {
        if ($this->_isRecollectTotal == true || in_array($this->getQuote()->getShippingAddress()->getShippingMethod(), array(
                'xpayment_pickup_shipping_xpayment_pickup_shipping',
                'freeshipping_freeshipping',
            )) && $flag == false
        ) {
            $this->_isRecollectTotal = true;
            return;
        } else {
//           TODO: Recollect shipping rates to quote.
            $this->_isRecollectTotal = true;
            $this->getQuote()->setTotalsCollectedFlag(false);
            Mage::getSingleton('adminhtml/sales_order_create')->collectShippingRates();
        }
    }


    public function getTotalData($total_code)
    {
        $reconn = true;
        if(Mage::getModel('xpos/integrate')->isIntegrateWithGiftVoucher())
            $reconn = false;
        $this->reCollectTotal($reconn);
        $totals = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getTotals();
        $value = 0;
        if (!empty($totals[$total_code]) && $totals[$total_code] instanceof Varien_Object) {
            $value = $totals[$total_code]->getData('value');
        }
        return $value;
    }
}

