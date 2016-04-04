<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class SM_StoreCredit_Model_Method_StoreCredit extends Mage_Payment_Model_Method_Abstract
{

    protected $_code  = 'sm_store_credit';
    protected $_formBlockType = 'sm_store_credit/form_storeCredit';
    protected $_infoBlockType = 'sm_store_credit/info_storeCredit';
    protected $_canCapture = true;
 //
    const PAY_CODE = 'sm_store_credit_pay';
    const PAY_PREVIOUS_CODE = 'sm_store_credit_pay_previous';
    const PAY_PREVIOUS_VALUE_CODE = 'sm_store_credit_pay_previous_value';

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
//        SM_XPos_Adminhtml
//        xpos
        if (Mage::app()->getRequest()->getControllerName() !== 'xpos') {
            return false;
        }
        if (!$quote->getCustomer() && !$quote->getCustomer()->getId()) {
            return false;
        }
//        if (Mage::helper('sm_store_credit')->getBalance() <= 0) {
//            return false;
//        }
        if (!parent::isAvailable($quote)) {
            return false;
        }
        return true;
    }

    public function getTitle()
    {
        $title = $this->getConfigData('title');
        $balance = Mage::helper('core')->formatPrice(Mage::helper('sm_store_credit')->getCurrentBalance(), false);
        return $title . " ($balance)";
    }

//    public function assignData($data)
//    {
//        $info = $this->getInfoInstance();
//
//        if ($data->getData(self::PAY_PREVIOUS_CODE))
//        {
//            $info->setData(self::PAY_PREVIOUS_CODE, $data->getData(self::PAY_PREVIOUS_CODE));
//        }
//
//        if ($data->getData(self::PAY_PREVIOUS_VALUE_CODE))
//        {
//            $info->setData(self::PAY_PREVIOUS_VALUE_CODE, $data->getData(self::PAY_PREVIOUS_VALUE_CODE));
//        }
//
//        return $this;
//    }

    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);

        $order = $payment->getOrder();
        $balance = Mage::getModel('credit/balance')->loadByCustomer($order->getCustomerId());
        $balance->addTransaction(
            -1 * $order->getGrandTotal(),
            Mirasvit_Credit_Model_Transaction::ACTION_USED,
            array('order' => $order)
        );
//        if ($this->getPayPreviousCredit()) {
//            $value = (float) $this->getPayPreviousCreditValue();
//            if ($value && $value > 0) {
//                $balance->addTransaction(
//                    $value,
//                    Mirasvit_Credit_Model_Transaction::ACTION_MANUAL,
//                    'Pay previous credit'
//                );
//            }
//        }
    }

//    public function getPayCredit()
//    {
//        $key = self::PAY_CODE;
//        return $this->getInfoInstance()->getData($key);
//    }
//
//    public function getPayPreviousCredit()
//    {
//        $key = self::PAY_PREVIOUS_CODE;
//        return $this->getInfoInstance()->getData($key);
//    }
//
//    public function getPayPreviousCreditValue()
//    {
//        $key = self::PAY_PREVIOUS_VALUE_CODE;
//        return $this->getInfoInstance()->getData($key);
//    }

}
