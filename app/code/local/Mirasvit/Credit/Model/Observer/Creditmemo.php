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



class Mirasvit_Credit_Model_Observer_Creditmemo extends Mirasvit_Credit_Model_Observer_Abstract
{
    public function onSaveAfter($observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getAutomaticallyCreated()) {
            if (Mage::helper('credit')->isAutoRefundEnabled()) {
                $creditmemo->setCreditRefundFlag(true)
                    ->setCreditTotalRefunded($creditmemo->getCreditAmount())
                    ->setBaseCreditTotalRefunded($creditmemo->getBaseCreditAmount());
            } else {
                return $this;
            }
        }

        $creditReturnMax = floatval($creditmemo->getCreditReturnMax());
        
        if (round($creditmemo->getCreditTotalRefunded(), 2) > round($creditReturnMax, 2)) {
            Mage::throwException(Mage::helper('credit')->__('Store credit amount cannot exceed order amount.'));
        }

        if ($creditmemo->getCreditRefundFlag() && $creditmemo->getBaseCreditTotalRefunded()) {
            $order->setBaseCreditTotalRefunded(
                $order->getBaseCreditTotalRefunded() + $creditmemo->getBaseCreditTotalRefunded()
            );
            $order->setCreditTotalRefunded(
                $order->getCreditTotalRefunded() + $creditmemo->getCreditTotalRefunded()
            );

            $balance = Mage::getModel('credit/balance')->loadByCustomer($order->getCustomerId());
            $balance->addTransaction(
                $creditmemo->getBaseCreditTotalRefunded(),
                Mirasvit_Credit_Model_Transaction::ACTION_REFUNDED,
                array('order' => $order, 'creditmemo' => $creditmemo)
            );
        }

        return $this;
    }

    public function onRegisterBefore($observer)
    {
        $request = $observer->getEvent()->getRequest();

        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $request->getParam('creditmemo');

        if (isset($input['refund_to_credit_enabled']) && isset($input['refund_to_credit_amount'])) {
            $enable = $input['refund_to_credit_enabled'];
            $amount = floatval($input['refund_to_credit_amount']);

            if ($enable && $amount) {
                $amount = min($creditmemo->getBaseCreditReturnMax(), $amount);

                if ($amount > 0) {
                    $amount = $creditmemo->getStore()->roundPrice($amount);
                    $creditmemo->setBaseCreditTotalRefunded($amount);

                    $amount = $creditmemo->getStore()->roundPrice($amount);

                    $creditmemo->setCreditTotalRefunded($amount);
                    $creditmemo->setCreditRefundFlag(true);
                    $creditmemo->setPaymentRefundDisallowed(false);
                }
            }
        }

        return $this;
    }

    public function onRefund($observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        /** @var Mage_Sales_Model_Order $order */
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseCreditAmount()) {
            if ($creditmemo->getRefundCredit()) {
                $baseAmount = $creditmemo->getBaseCreditAmount();
                $amount = $creditmemo->getCreditAmount();

                $creditmemo->setBaseCreditTotalRefunded($creditmemo->getBaseCreditTotalRefunded() + $baseAmount);
                $creditmemo->setCreditTotalRefunded($creditmemo->getCreditTotalRefunded() + $amount);
            }

            $order->setBaseCreditRefunded(
                $order->getBaseCreditRefunded() + $creditmemo->getBaseCreditAmount()
            );

            $order->setCreditRefunded(
                $order->getCreditRefunded() + $creditmemo->getCreditAmount()
            );

            if ($order->getCreditInvoiced() > 0
                && $order->getCreditInvoiced() == $order->getCreditRefunded()
            ) {
                $order->setForcedCanCreditmemo(false);
            }
        }

        return $this;
    }
}
