<?php
/**
 * Created by PhpStorm.
 * User: tungomi
 * Date: 10/19/2015
 * Time: 4:10 PM
 */
class SM_XPayment_Model_BluePay extends Mage_Payment_Model_Method_Abstract{

    protected $_code = 'xpayment_bluepaypayment';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;
    //protected $_isGateway = true;
    //protected $_canAuthorize = true;

    public function authorize(Varien_Object $payment, $amount) {
        Mage::log("Dummypayment\tIn authorize");
        return $this;
    }
}