<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Store Credit & Refund
 * @version   1.0.0
 * @build     307
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Credit_Block_Adminhtml_Sales_Order_Create_Payment extends Mage_Core_Block_Template
{
    protected $balance;

    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    public function formatPrice($value)
    {
        return Mage::getSingleton('adminhtml/session_quote')->getStore()->formatPrice($value);
    }

    public function getBalance()
    {
        if (!$this->_getBalance()) {
            return false;
        }

        return $this->_getBalance()->getAmount();
    }

    public function getUseCredit()
    {
        return $this->_getOrderCreateModel()->getQuote()->getUseCredit();
    }

    public function isFullyPaid()
    {
        if (!$this->_getBalance()) {
            return false;
        }

        return $this->_getBalance()->isFullAmountCovered($this->_getOrderCreateModel()->getQuote());
    }

    public function canUseCredit()
    {
        return $this->getBalance() > 0;
    }

    protected function _getBalance()
    {
        if (!$this->balance) {
            $quote = $this->_getOrderCreateModel()->getQuote();

            if (!$quote || !$quote->getCustomerId()) {
                return false;
            }

            $this->balance = Mage::getModel('credit/balance')->loadByCustomer($quote->getCustomerId());
        }

        return $this->balance;
    }

    public function canUseCustomerBalance()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();

        return $this->getBalance() && ($quote->getBaseGrandTotal() + $quote->getBaseCustomerBalanceAmountUsed() > 0);
    }
}
