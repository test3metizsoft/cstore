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



class Mirasvit_Credit_Block_Adminhtml_Customer_Edit_Tab_Credit extends Mage_Adminhtml_Block_Widget_Form implements
    Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();

        $this->setTitle(Mage::helper('credit')->__('Store Credit'));

        $this->setTemplate('mst_credit/customer/edit/tab/credit.phtml');
    }

    public function getTabLabel()
    {
        return $this->getTitle();
    }

    public function getTabTitle()
    {
        return $this->getTitle();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        if ($this->_getCustomer()) {
            return false;
        }

        return true;
    }

    public function getAfter()
    {
        return 'tags';
    }

    protected function _getCustomer()
    {
        if (Mage::registry('current_customer') && Mage::registry('current_customer')->getId() > 0) {
            return Mage::registry('current_customer');
        }

        return false;
    }

    protected function _prepareLayout()
    {
        if ($this->_getCustomer()) {
            $this->initForm();

            $this->setChild(
                'grid',
                $this->getLayout()->createBlock('credit/adminhtml_customer_edit_tab_credit_grid', 'credit.grid')
            );
        }

        return parent::_prepareLayout();
    }

    public function initForm()
    {
        $balance = Mage::getModel('credit/balance')->loadByCustomer($this->_getCustomer());

        $form = new Varien_Data_Form();


        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => Mage::helper('credit')->__('Balance Information'))
        );

        $fieldset->addField('balance_amount', 'label', array(
            'label' => Mage::helper('credit')->__('Current Balance'),
            'value' => Mage::helper('core')->currency($balance->getAmount(), true, false)
        ));

        $updatedAt = strtotime($balance->getUpdatedAt());
        $fieldset->addField('balance_change', 'label', array(
            'label' => Mage::helper('credit')->__('Last Change'),
            'value' => $updatedAt > 0 ? Mage::getSingleton('core/date')->date('M, d Y h:i A', $updatedAt) : '-',
        ));

        $fieldset->addField('is_subscribed', 'label', array(
            'label' => Mage::helper('credit')->__('Subscribed to email notifications?'),
            'value' => $balance->getIsSubscribed()
                ? Mage::helper('credit')->__('Yes')
                : Mage::helper('credit')->__('No'),
        ));

        $fieldset->addField('add_transaction', 'link', array(
            'value' => Mage::helper('credit')->__('Add New Transaction'),
            'href'  => Mage::helper('adminhtml')->getUrl(
                'adminhtml/credit_transaction/add',
                array('customer_id' => $this->_getCustomer()->getId())
            )
        ));

        $this->setForm($form);
        return $this;
    }

    public function getStatusChangedDate()
    {
        $subscriber = Mage::registry('subscriber');
        if ($subscriber->getChangeStatusAt()) {
            return $this->formatDate(
                $subscriber->getChangeStatusAt(),
                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                true
            );
        }

        return null;
    }
}
