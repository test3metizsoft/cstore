<?php
/**
 * Created by PhpStorm.
 * User: vjcpsy
 * Date: 6/8/2015
 * Time: 17:19
 */ 
class SM_XPos_Model_Sales_Order_Invoice_Total_Discount extends Mage_Sales_Model_Order_Invoice_Total_Discount {
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        parent::collect($invoice);

        $totalDiscountAmount = $invoice->getDiscountAmount();
        $baseTotalDiscountAmount = $invoice->getBaseDiscountAmount();

        $order = $invoice->getOrder();
        /*    check Xpos discount*/

        if (!$this->isXPosOrder($order)) return $this;

        $discountFromXpos = $order->getDiscountAmount();
        if ($discountFromXpos != 0) {
            $totalDiscountAmount += -$discountFromXpos;
            $baseTotalDiscountAmount += -$discountFromXpos;
        }
        $invoice->setDiscountAmount(-$totalDiscountAmount);
        $invoice->setBaseDiscountAmount(-$baseTotalDiscountAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);



        return $this;
    }

    protected function isXPosOrder($order)
    {
        return !!$order->getData('xpos');
    }


}
