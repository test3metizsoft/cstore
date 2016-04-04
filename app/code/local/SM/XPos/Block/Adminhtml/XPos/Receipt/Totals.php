<?php

/**
 * Created by PhpStorm.
 * User: Huy
 * Date: 6/30/2015
 * Time: 8:45 AM
 */
class SM_XPos_Block_Adminhtml_XPos_Receipt_Totals extends Mage_Core_Block_Template
{
    protected $additionFieldsToDisplay = array();

    public function _construct()
    {
        $this->setTemplate('sm/xpos/receipt/totals.phtml');

        if ($this->getRequest()->getParam('xpos_receipt_addition_fields_to_display') != null) {
            /*
             * For previewing
             */
            $this->additionFieldsToDisplay = explode(',', $this->getRequest()->getParam('xpos_receipt_addition_fields_to_display'));
        } else {
            $this->additionFieldsToDisplay = explode(',', Mage::getStoreConfig('xpos/receipt/addition_fields_to_display'));
        }
    }

    /*
     * Get Subtotal
     */
    public function getSubtotal()
    {
        $subtotal = Mage::helper('tax')->displaySalesSubtotalExclTax() ? $this->getOrder()->getSubtotal() : $this->getOrder()->getSubtotalInclTax();
        return $this->getOrder()->formatPrice($subtotal);
    }

    /*
     * Get Discount amount
     * SmartOSC's old approach
     */
    public function getDiscountAmount()
    {
        $discountAmount = 0;
        if ($this->getOrder()->getData('discount_amount') != 0) {
            $discountAmount = str_replace('-', '', $this->getOrder()->getData('discount_amount'));
        }
        return $discountAmount;
    }

    public function getGiftCardsAmount()
    {
        if (!$this->getOrder()->getGiftCardsAmount() || $this->getOrder()->getGiftCardsAmount() <= 0) {
            return false;
        }
        return $this->getOrder()->formatPrice($this->getOrder()->getGiftCardsAmount());
    }

    public function getGiftCards()
    {
        if (!$this->getOrder()->getGiftCardsAmount() || $this->getOrder()->getGiftCardsAmount() <= 0) {
            return false;
        }
        return unserialize($this->getOrder()->getGiftCards());
    }

    public function getShippingAmount()
    {
        switch (Mage::getStoreConfig('tax/sales_display/shipping')) {
            case '1': #Exclude
                return $this->getOrder()->getShippingAmount();
            case '2': #Include
                return $this->getOrder()->getShippingInclTax();
            case '3': #Exclude & Include
                #return $this->getOrder()->formatPrice($this->getOrder()->getShippingAmount()) . " (Incl. Tax {$this->getOrder()->formatPrice($this->getOrder()->getShippingInclTax())})";
                return $this->getOrder()->getShippingInclTax();
        }
    }

    public function formatPrice($price = 0)
    {
        return $this->getOrder()->formatPrice($price);
    }

    public function getTaxesAmount()
    {
        return $this->getOrder()->formatPrice($this->getOrder()->getTaxAmount());
    }

    public function getStoreCredit()
    {
        if ($this->getOrder()->getCustomerBalanceAmount() != 0) {
            return $this->getOrder()->formatPrice($this->getOrder()->getCustomerBalanceAmount());
        }
        return false;
    }

    public function getGrandTotal()
    {
        return $this->getOrder()->formatPrice($this->getOrder()->getGrandTotal());
    }

    public function getTotalPaid()
    {
        return $this->getOrder()->formatPrice($this->getOrder()->getTotalPaid());

    }

    public function getBalance()
    {
        $balance = $this->getOrder()->getTotalPaid() - $this->getOrder()->getGrandTotal();

        if ($balance > 0) {
            return $this->getOrder()->formatPrice($balance);
        }
        return false;
    }

    public function getChange()
    {
        $orderId = $this->getOrder()->getIncrementId();
        $transaction = $this->loadByField($this->getTransaction(), 'order_id', $orderId);
        $cashIn = 0;
        foreach ($transaction as $tran) {
            $cashIn += $tran->getData('cash_in');
        }
        $balance = $cashIn - $this->getOrder()->getGrandTotal();

        if ($balance > 0) {
            return $this->getOrder()->formatPrice($balance);
        }
        return false;
    }


    public function getTotalPaidFromTransaction()
    {
        $orderId = $this->getOrder()->getIncrementId();
        $transaction = $this->loadByField($this->getTransaction(), 'order_id', $orderId);
        $cashIn = 0;
        foreach ($transaction as $tran) {
            $cashIn += $tran->getData('cash_in');
        }
        return $this->getOrder()->formatPrice($cashIn);
    }

    private function getTransaction()
    {
        return Mage::getSingleton('xpos/transaction')->getCollection();
    }

    public function loadByField($collection, $field, $value)
    {
        $collection = $collection
            ->addFieldToFilter($field, array('eq' => $value));

        return $collection;
    }

    public function displayPrices($baseAmount, $amount)
    {
        return $this->helper('adminhtml/sales')->displayPrices($this->getOrder(), $baseAmount, $amount);
    }

    public function getPaymentAdditionalData()
    {
        $additionalData = @unserialize($this->getOrder()->getPayment()->getAdditionalData());
        if(isset($additionalData['splitPayment'])){
            $additionalData = $additionalData['splitPayment'];
        }

        foreach($additionalData as $payment => $value){
            if ($value == 0 || $payment == 'sm_store_credit_due'){
                unset($additionalData[$payment]);
            }
        }

        return $additionalData;
    }

    public function getConfigDataPaymentMethod($code, $field) {
        $path = 'payment/' . $code . '/' . $field;

        return Mage::getStoreConfig($path);
    }

    public function getSmPayDueAmount()
    {
        $amount = $this->getOrder()->getData('sm_pay_due_amount');
        if( $amount < 0){
            return $this->getOrder()->formatPrice(-$amount);
        }

        return false;
    }

    public function getSmCurrentBalance()
    {
        $balance = $this->getOrder()->getData('sm_current_balance');
        return $this->getOrder()->formatPrice($balance);
    }
}
