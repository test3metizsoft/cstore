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



class Mirasvit_Credit_Block_Checkout_Cart_Credit extends Mage_Core_Block_Template
{
    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getBalance()
    {
        if ($this->getCustomer()->getId() > 0) {
            return Mage::getModel('credit/balance')->loadByCustomer($this->getCustomer());
        }

        return false;
    }

    public function getAmountToUse()
    {
        $toUse = Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();

        if ($toUse > $this->getBalance()->getAmount()) {
            $toUse = $this->getBalance()->getAmount();
        }

        return $toUse;
    }

    public function getUsedAmount()
    {
        return Mage::getModel('checkout/cart')->getQuote()->getCreditAmountUsed();
    }
}
