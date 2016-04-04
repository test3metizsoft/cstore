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



class Mirasvit_Credit_Block_Adminhtml_Transaction_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $transaction = Mage::registry('current_transaction');

        $fieldset = $form->addFieldset('edit_fieldset', array(
            'legend' => Mage::helper('credit')->__('General Information')));

        if ($transaction->getId()) {
            $fieldset->addField('transaction_id', 'hidden', array(
                'name'  => 'transaction_id',
                'value' => $transaction->getId(),
            ));
        }

        $fieldset->addField('customer_id', 'hidden', array(
            'label'    => Mage::helper('credit')->__('Customer ID'),
            'required' => true,
            'name'     => 'customer_id',
            'value'    => $transaction->getCustomerId(),
        ));

        if ($transaction->getCustomerId() > 0) {
            $customer = Mage::getModel('customer/customer')->load($transaction->getCustomerId());

            $fieldset->addField('customer_name', 'label', array(
                'label' => Mage::helper('credit')->__('Customer'),
                'value' => $customer->getFirstname()
                    . ' '
                    . $customer->getLastname()
                    . ' <'
                    . $customer->getEmail()
                    . '>',
            ));
        }

        $fieldset->addField('balance_delta', 'text', array(
            'label'    => Mage::helper('credit')->__('Store Credit Balance Change'),
            'required' => true,
            'name'     => 'balance_delta',
            'value'    => $transaction->getBalanceDelta(),
        ));

        $fieldset->addField('message', 'text', array(
            'label'    => Mage::helper('credit')->__('Additional Message'),
            'required' => true,
            'name'     => 'message',
            'value'    => $transaction->getMessage(),
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
