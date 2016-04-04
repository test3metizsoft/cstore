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



class Mirasvit_Credit_Model_Observer
{
    public function customerSaveAfter()
    {
        return $this;
    }

    public function addPaypalCreditItem($observer)
    {
        $payPalCart = $observer->getEvent()->getPaypalCart();

        if ($payPalCart) {
            $salesEntity = $payPalCart->getSalesEntity();
            if ($salesEntity instanceof Mage_Sales_Model_Quote) {
                $balanceField = 'base_credit_amount_used';
            } elseif ($salesEntity instanceof Mage_Sales_Model_Order) {
                $balanceField = 'base_credit_amount';
            } else {
                return;
            }

            $value = abs($salesEntity->getDataUsingMethod($balanceField));

            if ($value > 0.0001) {
                $payPalCart->updateTotal(
                    Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,
                    floatval($value),
                    Mage::helper('credit')->__(
                        'Store Credit (%s)',
                        Mage::app()->getStore()->convertPrice($value, true, false)
                    )
                );
            }
        }
    }
}
