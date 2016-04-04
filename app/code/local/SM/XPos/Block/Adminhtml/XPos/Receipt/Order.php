<?php
/**
 * Created by PhpStorm.
 * User: Huy
 * Date: 6/29/2015
 * Time: 4:42 PM
 */

class SM_XPos_Block_Adminhtml_XPos_Receipt_Order extends Mage_Core_Block_Template {
    public function _construct() {
        $this->setTemplate('sm/xpos/receipt/order.phtml');
    }

    /*
     * Get Order's Cashier
     */
    public function getCashier() {
        if ($this->getOrder()->getXposUserId() != null) {
            return $cashier = Mage::getModel('xpos/user')->load($this->getOrder()->getXposUserId());
        }
        return false;
    }
    public function getPaymentMethodTitle() {
        if ($this->getOrder()->getPayment() == null)
            return self::DEFAULT_NO_PAYMENT_METHOD_TITLE;
        $paymentCode = $this->getOrder()->getPayment()->getMethodInstance()->getCode();
        $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title', Mage::helper('xpos/product')->getCurrentSessionStoreId());
        return $paymentTitle;
    }
}
