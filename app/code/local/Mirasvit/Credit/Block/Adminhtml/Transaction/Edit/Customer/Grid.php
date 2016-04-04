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



class Mirasvit_Credit_Block_Adminhtml_Transaction_Edit_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('credit_transaction_edit_customer_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email');

        if (Mage::registry('current_transaction') && Mage::registry('current_transaction')->getCustomerId() > 0) {
            $collection->addFieldToFilter('entity_id', Mage::registry('current_transaction')->getCustomerId());
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('credit')->__('ID'),
            'width'  => '50px',
            'index'  => 'entity_id',
            'align'  => 'right',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('credit')->__('Name'),
            'index'  => 'name'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('credit')->__('Email'),
            'index'  => 'email'
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('customer_id');

        $this->getMassactionBlock()->addItem('select', array(
            'label' => Mage::helper('credit')->__('Select'),
        ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadCustomerBlock', array('block' => 'customer_grid'));
    }

    protected function _getUsers($json = false)
    {
        if ($this->getRequest()->getParam('in_role_user') != "") {
            return $this->getRequest()->getParam('in_role_user');
        }

        $roleId = $this->getRequest()->getParam('rid') > 0
            ? $this->getRequest()->getParam('rid')
            : Mage::registry('RID');

        $users = Mage::getModel('api/roles')->setId($roleId)->getRoleUsers();

        if (sizeof($users) > 0) {
            if ($json) {
                $jsonUsers = array();

                foreach ($users as $usrid) {
                    $jsonUsers[$usrid] = 0;
                }

                return Mage::helper('core')->jsonEncode((object)$jsonUsers);
            } else {
                return array_values($users);
            }
        } else {
            if ($json) {
                return '{}';
            } else {
                return array();
            }
        }
    }
}
