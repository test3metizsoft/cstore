<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/6/16
 * Time: 10:51 PM
 */
class SM_StoreCredit_Model_Observer
{

    public function salesOrderInvoicePay($event)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $event->getInvoice();
        if (!$invoice || !$invoice instanceof Mage_Sales_Model_Order_Invoice) {
            return;
        }
        $order = $invoice->getOrder();
        $payDue = floatval($order->getData('sm_pay_due_amount'));
        if ($payDue < 0) return;

        if (!$order->getCustomerId()) return;

        $balance = Mage::getModel('credit/balance')->loadByCustomer($order->getCustomerId());
        $balance->addTransaction(
            $payDue,
            Mirasvit_Credit_Model_Transaction::ACTION_MANUAL,
            'Pay previous credit'
        );

        $order->setData('sm_pay_due_amount', -$payDue);
    }

    public function salesOrderPlaceBefore($event)
    {
        $order = $event->getOrder();
        if (!$order || !$order instanceof Mage_Sales_Model_Order) {
            return;
        }
        $payDue = floatval($order->getData('sm_pay_due_amount'));
        if ($payDue < 0) return;

        if (!$order->getCustomerId()) return;

        $balance = Mage::getModel('credit/balance')->loadByCustomer($order->getCustomerId());
        $balance->addTransaction(
            $payDue,
            Mirasvit_Credit_Model_Transaction::ACTION_MANUAL,
            'Pay previous credit'
        );

        $order->setData('sm_pay_due_amount', -$payDue);
    }


}