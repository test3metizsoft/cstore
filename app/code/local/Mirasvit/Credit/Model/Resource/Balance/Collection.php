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



class Mirasvit_Credit_Model_Resource_Balance_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('credit/balance');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();

        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('credit')->__('-- Please Select --'));
        }

        foreach ($this as $item) {
            $arr[] = array('value' => $item->getId(), 'label' => $item->getName());
        }

        return $arr;
    }

    public function getOptionArray($emptyOption = false)
    {
        $arr = array();

        if ($emptyOption) {
            $arr[0] = Mage::helper('credit')->__('-- Please Select --');
        }

        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    protected function initFields()
    {
        $this->_joinCustomer();

        return $this;
    }

    protected function _joinCustomer()
    {
        $firstname = Mage::getSingleton('eav/config')->getAttribute('customer', 'firstname');
        $lastname = Mage::getSingleton('eav/config')->getAttribute('customer', 'lastname');
        $nameExpr = new Zend_Db_Expr('CONCAT(customer_firstname.value, " ", customer_lastname.value)');

        $this->getSelect()->joinLeft(
            array('customer' => $this->getTable('customer/entity')),
            'main_table.customer_id = customer.entity_id',
            array('email' => 'email')
        )
            ->joinLeft(
                array('customer_firstname' => $firstname->getBackendTable()),
                'customer.entity_id = customer_firstname.entity_id
                    AND customer_firstname.attribute_id = ' . $firstname->getAttributeId(),
                array('firstname' => 'value')
            )
            ->joinLeft(
                array('customer_lastname' => $lastname->getBackendTable()),
                'customer.entity_id = customer_lastname.entity_id
                    AND customer_lastname.attribute_id = ' . $lastname->getAttributeId(),
                array('lastname' => 'value')
            )
            ->columns(array('name' => $nameExpr));

        return $this;
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->initFields();
    }
}
