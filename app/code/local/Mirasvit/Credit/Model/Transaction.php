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



class Mirasvit_Credit_Model_Transaction extends Mage_Core_Model_Abstract
{
    const ACTION_MANUAL = 'manual';
    const ACTION_REFUNDED = 'refunded';
    const ACTION_USED = 'used';

    protected $balance = null;

    protected function _construct()
    {
        $this->_init('credit/transaction');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    public function getBalance()
    {
        if (!$this->getBalanceId()) {
            return false;
        }

        if ($this->balance === null) {
            $this->balance = Mage::getModel('credit/balance')->load($this->getBalanceId());
        }

        return $this->balance;
    }

    public function getActionLabel()
    {
        $actions = Mage::getSingleton('credit/system_config_source_action')->toOptionArray();

        if (isset($actions[$this->getAction()])) {
            return $actions[$this->getAction()];
        }
    }

    public function getBackendMessage()
    {
        return Mage::helper('credit')->getBackendTransactionMessage($this->getMessage());
    }

    public function getFrontendMessage()
    {
        return Mage::helper('credit')->getFrontendTransactionMessage($this->getMessage());
    }

    public function getEmailMessage()
    {
        return Mage::helper('credit')->getEmailTransactionMessage($this->getMessage());
    }
}
