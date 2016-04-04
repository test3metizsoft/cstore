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



class Mirasvit_Credit_Model_Balance extends Mage_Core_Model_Abstract
{
    protected $transactionCollection;
    protected $customer = null;

    protected function _construct()
    {
        $this->_init('credit/balance');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    public function getTransactionCollection()
    {
        if (!$this->transactionCollection) {
            $this->transactionCollection = Mage::getModel('credit/transaction')->getCollection()
                ->addFieldToFilter('balance_id', $this->getBalanceId());
        }

        return $this->transactionCollection;
    }

    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }

        if ($this->customer === null) {
            $this->customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        }

        return $this->customer;
    }

    public function loadByCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        $balance = Mage::getModel('credit/balance')->getCollection()
            ->addFieldToFilter('customer_id', $customer)
            ->getFirstItem();

        if ($balance->getId()) {
            return $balance;
        }

        $this->setCustomerId($customer)
            ->setIsSubscribed(1)
            ->setAmount(0)
            ->save();

        return $this;
    }

    public function addTransaction($balanceDelta, $action = null, $message = null)
    {
        $balanceDelta = floatval($balanceDelta);

        if ($balanceDelta == 0) {
            return false;
        }

        if ($action == null) {
            $action = Mirasvit_Credit_Model_Transaction::ACTION_MANUAL;
        }

        if (is_array($message)) {
            $message = Mage::helper('credit')->createTransactionMessage($message);
        }

        $this->setAmount($this->getAmount() + $balanceDelta);

        $transaction = Mage::getModel('credit/transaction')
            ->setBalanceId($this->getId())
            ->setBalanceDelta($balanceDelta)
            ->setBalanceAmount($this->getAmount())
            ->setAction($action)
            ->setMessage($message)
            ->save();

        $this->save();

        if (Mage::helper('credit/mail')->sendBalanceUpdateEmail($transaction)) {
            $transaction->setIsNotified(true)
                ->save();
        }

        return $this;
    }
}
