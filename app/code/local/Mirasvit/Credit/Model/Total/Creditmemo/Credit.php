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



class Mirasvit_Credit_Model_Total_Creditmemo_Credit extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setBaseCreditTotalRefunded(0)
            ->setCreditTotalRefunded(0)
            ->setBaseCreditReturnMax(0)
            ->setCreditReturnMax(0);

        $order = $creditmemo->getOrder();

        if ($order->getBaseCreditAmount() && $order->getBaseCreditInvoiced()) {
            $left = $order->getBaseCreditInvoiced() - $order->getBaseCreditRefunded();

            $used = 0;
            $baseUsed = 0;

            if ($left >= $creditmemo->getBaseGrandTotal()) {
                $baseUsed = $creditmemo->getBaseGrandTotal();
                $used = $creditmemo->getGrandTotal();

                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setGrandTotal(0);

                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $baseUsed = $order->getBaseCreditInvoiced() - $order->getBaseCreditRefunded();
                $used = $order->getCreditInvoiced() - $order->getCreditRefunded();

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseUsed);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $used);
            }

            $creditmemo->setBaseCreditAmount($baseUsed);
            $creditmemo->setCreditAmount($used);
        }

        $creditmemo->setBaseCreditReturnMax(
                $creditmemo->getBaseCreditReturnMax()
                + $creditmemo->getBaseGrandTotal()
                + $creditmemo->getBaseCreditAmount()
        );

        $creditmemo->setCreditReturnMax($creditmemo->getBaseCreditReturnMax());

        return $this;
    }
}
