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



class Mirasvit_Credit_Model_Total_Quote_Credit extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('credit');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        if (!$quote->getCreditCollected()) {
            $quote->setBaseCreditAmountUsed(0)
                ->setCreditAmountUsed(0)
                ->setCreditCollected(true);
        }

        $baseBalanceTotalUsed = $balanceTotalUsed = $baseBalanceUsed = $balanceUsed = 0;

        $baseBalance = $balance = 0;

        if ($quote->getCustomer()->getId()) {
            if ($quote->getUseCredit()) {
                $baseBalance = Mage::getModel('credit/balance')->loadByCustomer($quote->getCustomerId())->getAmount();
                $balance = $quote->getStore()->convertPrice($baseBalance);

            }
        }

        $baseBalanceLeft = $baseBalance - $quote->getBaseCreditAmountUsed();
        $balanceLeft = $balance - $quote->getCreditAmountUsed();

        $baseBalanceUsed = $baseBalanceLeft;
        $balanceUsed = $balanceLeft;


        if ($baseBalanceUsed > $address->getBaseGrandTotal()) {
            $baseBalanceUsed = $address->getBaseGrandTotal();
            $balanceUsed = $address->getGrandTotal();
        }

        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseBalanceUsed)
            ->setGrandTotal($address->getGrandTotal() - $balanceUsed);

        $baseBalanceTotalUsed = $quote->getBaseCreditAmountUsed() + $baseBalanceUsed;
        $balanceTotalUsed = $quote->getCreditAmountUsed() + $balanceUsed;


        $quote->setBaseCreditAmountUsed($baseBalanceTotalUsed)
            ->setCreditAmountUsed($balanceTotalUsed);

        $address->setBaseCreditAmount($baseBalanceUsed)
            ->setCreditAmount($balanceUsed);

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getCreditAmount()) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => Mage::helper('credit')->__('Store Credit'),
                'value' => -$address->getCreditAmount(),
            ));
        }

        return $this;
    }
}
