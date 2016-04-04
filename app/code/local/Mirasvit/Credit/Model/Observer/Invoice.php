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



class Mirasvit_Credit_Model_Observer_Invoice extends Mirasvit_Credit_Model_Observer_Abstract
{
    public function onSaveAfter($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        // is new invoice
        if ($invoice->getOrigData() === null && $invoice->getBaseCreditAmount()) {
            $order->setBaseCreditInvoiced($order->getBaseCreditInvoiced() + $invoice->getBaseCreditAmount())
                ->setCreditInvoiced($order->getCreditInvoiced() + $invoice->getCreditAmount());
        }

        $order->getResource()->saveAttribute($order, 'base_credit_invoiced');
        $order->getResource()->saveAttribute($order, 'credit_invoiced');

        return $this;
    }
}
