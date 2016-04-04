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



class Mirasvit_Credit_Block_Customer_Account extends Mage_Core_Block_Template
{
    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getBalance()
    {
        return Mage::getModel('credit/balance')->loadByCustomer($this->getCustomer());
    }

    public function getTransactionCollection()
    {
        return Mage::getModel('credit/transaction')->getCollection()
            ->addFieldToFilter('main_table.balance_id', $this->getBalance()->getId())
            ->setOrder('main_table.created_at', 'desc');
    }

    public function getSend2FriendUrl()
    {
        return Mage::getUrl('credit/account/send2friend');
    }

    public function getSend2FriendFormData()
    {
        return new Varien_Object(Mage::getSingleton('customer/session')->getSend2FriendFormData());
    }
}
