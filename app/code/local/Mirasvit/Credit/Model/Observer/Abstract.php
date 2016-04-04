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



class Mirasvit_Credit_Model_Observer_Abstract
{
    protected function _importPaymentData($quote, $payment, $isUseCredit)
    {
        if (!$quote || !$quote->getCustomerId()
            || $quote->getBaseGrandTotal() + $quote->getBaseCreditAmountUsed() <= 0
        ) {
            return;
        }

        $quote->setUseCredit($isUseCredit);
        if ($isUseCredit) {
            $balance = Mage::getModel('credit/balance')->loadByCustomer($quote->getCustomerId());
            if ($balance) {
                $quote->setBalanceInstance($balance);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            } else {
                $quote->setUseCredit(false);
            }
        }
    }
}
