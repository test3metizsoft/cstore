<?php

class SM_StoreCredit_Helper_Data extends Mage_Core_Model_Abstract
{
    protected $_balance;

    public function getDueBalance()
    {
        if ($this->getBalance() && $this->getBalance() < 0) {
            return abs($this->getBalance());
        }
        return 0;
    }

    public function getCurrentBalance()
    {
        if ($this->getBalance() && $this->getBalance() > 0) {
            return $this->getBalance();
        }
        return 0;
    }

    public function getBalance()
    {
        if (!$this->_getBalance()) {
            return false;
        }

        return $this->_getBalance()->getAmount();
    }

    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    protected function _getBalance()
    {
        if (!$this->_balance) {
            $quote = $this->_getOrderCreateModel()->getQuote();

            if (!$quote || !$quote->getCustomerId()) {
                return false;
            }

            $this->_balance = Mage::getModel('credit/balance')->loadByCustomer($quote->getCustomerId());
        }

        return $this->_balance;
    }
}
