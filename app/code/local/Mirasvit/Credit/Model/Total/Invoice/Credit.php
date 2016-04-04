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



class Mirasvit_Credit_Model_Total_Invoice_Credit extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        #credit amount AND credit amount not set for invoice
        if ($order->getBaseCreditAmount()
            && floatval($order->getBaseCreditInvoiced()) == 0
        ) {
            $baseUsed = $order->getBaseCreditAmount();
            $used = $order->getCreditAmount();

            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseUsed)
                ->setGrandTotal($invoice->getGrandTotal() - $used)
                ->setBaseCreditAmount($baseUsed)
                ->setCreditAmount($used);
        }

        return $this;
    }
}
