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



class Mirasvit_Credit_Model_Observer_Order extends Mirasvit_Credit_Model_Observer_Abstract
{
    public function onCreateProcessData($observer)
    {
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();

        if (isset($request['payment']) && isset($request['payment']['use_credit'])) {
            $this->_importPaymentData($quote, $quote->getPayment(), (bool)$request['payment']['use_credit']);
        }
    }

    public function onLoadAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold() || $order->isCanceled()) {
            return $this;
        }

        if ($order->getCreditInvoiced() - $order->getCreditRefunded() > 0) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }

    public function onSubmitBefore($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $order->getQuote();

        $order->setBaseCreditAmount($quote->getBaseCreditAmountUsed())
            ->setCreditAmount($quote->getCreditAmountUsed())
            ->setBaseCustomerBalanceAmount($quote->getBaseCreditAmountUsed()) # compatiblity with PayPal HostedPro
        ;

        return $this;
    }

    public function onSubmitAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getBaseCreditAmount() > 0) {
            $balance = Mage::getModel('credit/balance')->loadByCustomer($order->getCustomerId());
            $balance->addTransaction(
                -1 * $order->getBaseCreditAmount(),
                Mirasvit_Credit_Model_Transaction::ACTION_USED,
                array('order' => $order)
            );
            if ($order->getBaseGrandTotal() == 0) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)
                    ->save();
            }
        }

        return $this;
    }

    public function onCancelAfter($observer)
    {
        if (!$observer->getEvent()->getItem()) {
            return $this;
        }

        $order = $observer->getEvent()->getItem()->getOrder();

        if ($order && $order->getBaseCreditAmount() > 0 && $order->getBaseCreditRefunded() == 0) {
            $balance = Mage::getModel('credit/balance')->loadByCustomer($order->getCustomerId());
            $balance->addTransaction(
                $order->getBaseCreditAmount(),
                Mirasvit_Credit_Model_Transaction::ACTION_REFUNDED,
                array('order' => $order)
            );

            $order->setBaseCreditRefunded($order->getBaseCreditAmount())
                ->setCreditRefunded($order->getCreditAmount())
                ->setBaseCreditTotalRefunded($order->getBaseCreditAmount())
                ->setCreditTotalRefunded($order->getCreditAmount())
                ->save();
        }

        return $this;
    }
}
