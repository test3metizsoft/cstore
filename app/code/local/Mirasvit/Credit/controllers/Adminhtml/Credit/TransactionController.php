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



class Mirasvit_Credit_Adminhtml_Credit_TransactionController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('sales');

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Transactions'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('credit/adminhtml_transaction'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_title($this->__('New Transaction'));

        $this->_initTransaction();

        $this->_initAction();
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Transaction  Manager'),
            Mage::helper('adminhtml')->__('Transaction Manager'),
            $this->getUrl('*/*/')
        );

        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Add Transaction '),
            Mage::helper('adminhtml')->__('Add Transaction')
        );

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('credit/adminhtml_transaction_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $customers = explode(',', $data['customer_id']);

            try {
                foreach ($customers as $customerId) {
                    $balance = Mage::getModel('credit/balance')->loadByCustomer($customerId);

                    $balance->addTransaction(
                        $data['balance_delta'],
                        Mirasvit_Credit_Model_Transaction::ACTION_MANUAL,
                        $data['message']
                    );
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Transaction(s) was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('adminhtml')->__('Unable to find transaction to save')
        );

        $this->_redirect('*/*/');
    }

    public function _initTransaction()
    {
        $transaction = Mage::getModel('credit/transaction');
        if ($this->getRequest()->getParam('id')) {
            $transaction->load($this->getRequest()->getParam('id'));
        }

        if ($this->getRequest()->getParam('customer_id')) {
            $transaction->setCustomerId($this->getRequest()->getParam('customer_id'));
        }

        Mage::register('current_transaction', $transaction);

        return $transaction;
    }

    public function loadCustomerBlockAction()
    {
        $result = $this->getLayout()->createBlock('credit/adminhtml_transaction_edit_customer')->toHtml();

        $this->getResponse()->setBody($result);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/credit/transaction');
    }
}
