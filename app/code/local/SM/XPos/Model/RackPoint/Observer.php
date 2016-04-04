<?php

/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 9/28/15
 * Time: 11:16 AM
 */
class SM_XPos_Model_RackPoint_Observer extends Rack_Point_Model_Observer {

    /**
     * Substract used point after customer place order
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function orderPlaceAfter($observer) {
        if ($this->isActive() == false) {
            return $this;
        }
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        //process receive point
        if (Mage::helper('rackpoint')->isActivePointOnPlacing()) {
            $order->setPointReceivedInvoiced($order->getPointReceived());
            $order->setRegisterForActiveReceivedPoint(true);
        }

        /*DO: phải get lại pointtUsed từ quote.*/
        $usedPoint = $observer->getEvent()->getOrder()->getQuote()->getPointUsed();
        $order->setPointUsed($usedPoint);


        if ($usedPoint > 0) {
            $balance = Mage::getModel('rackpoint/point_balance');
            $balance->subPointByPlaceOrder($order);
        }

        if ($order->getQuote() && $order->getQuote()->getCheckoutMethod(true) == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            $this->_addCustomerRegisterPoint($order->getCustomerId(), $order->getCustomer()->getWebsiteId());
        }

        Mage::getSingleton('checkout/session')->setPointUsed(0);

        return $this;
    }
}
