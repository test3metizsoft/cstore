<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/6/16
 * Time: 5:36 PM
 */

class SM_StoreCredit_Model_Total_Quote_Due extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    protected $_quoteAddress;

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_quoteAddress = $address;
        if ($this->_quoteAddress->getData('address_type') == 'billing') {
            return;
        }
        $hasDue = floatval(Mage::app()->getRequest()->getPost('sm_store_credit_pay_previous_checkbox'));
        $due = $hasDue ? floatval(Mage::app()->getRequest()->getPost('sm_store_credit_pay_previous_value')) : 0;
        $this->_quoteAddress->setTotalAmount('due', $due);
        $this->_quoteAddress->getQuote()->setData('sm_pay_due_amount', $due);
    }

}