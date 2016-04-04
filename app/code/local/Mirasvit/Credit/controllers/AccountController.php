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



class Mirasvit_Credit_AccountController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();

        if ($action != 'external' && $action != 'postexternal') {
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('credit')->__('Store Credit'));

        $this->renderLayout();
    }

    public function subscribeAction()
    {
        $balance = Mage::getModel('credit/balance')->loadByCustomer($this->_getSession()->getCustomer());
        $isSubscribed = (bool)$this->getRequest()->getParams('is_subscribed');

        $balance->setIsSubscribed($isSubscribed)
            ->save();

        $this->_getSession()->addSuccess(Mage::helper('credit')->__('Email subscription was successfully updated.'));

        $this->_redirect('*/*/');
    }

    public function send2friendAction()
    {
        $email = $this->getRequest()->getParam('email');
        $amount = floatval($this->getRequest()->getParam('amount'));
        $message = $this->getRequest()->getParam('message');

        if ($email && $amount > 0) {
            $friend = Mage::getModel('customer/customer');
            $friend->setWebsiteId(Mage::app()->getWebsite()->getId());
            $friend->loadByEmail($email);

            if ($friend->getId()) {
                $friendBalance = Mage::getModel('credit/balance')->loadByCustomer($friend);
                $myBalance = Mage::getModel('credit/balance')->loadByCustomer($this->_getSession()->getCustomer());

                if ($myBalance->getAmount() >= $amount) {
                    $myBalance->addTransaction(
                        -1 * $amount,
                        Mirasvit_Credit_Model_Transaction::ACTION_USED,
                        Mage::helper('credit')->__('Send to friend %s', $email)
                    );

                    $friendBalance->addTransaction(
                        $amount,
                        Mirasvit_Credit_Model_Transaction::ACTION_MANUAL,
                        Mage::helper('credit')->__(
                            'Received from %s %s',
                            $this->_getSession()->getCustomer()->getEmail(),
                            $message
                        )
                    );

                    $this->_getSession()->addSuccess(Mage::helper('credit')->__('Balance successfully send.'));

                    Mage::getSingleton('customer/session')->setSend2FriendFormData(false);
                } else {
                    Mage::getSingleton('customer/session')->setSend2FriendFormData($this->getRequest()->getPost());

                    $this->_getSession()->addError(Mage::helper('credit')->__('Amount cannot exceed balance amount.'));
                }
            } else {
                Mage::getSingleton('customer/session')->setSend2FriendFormData($this->getRequest()->getPost());

                $this->_getSession()->addError(Mage::helper('credit')->__('Customer with email %s not exist.', $email));
            }
        }

        $this->_redirect('*/*/');
    }
}
