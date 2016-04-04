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



class Mirasvit_Credit_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('grid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('credit/transaction')
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', array(
            'header'       => Mage::helper('credit')->__('Transaction #'),
            'type'         => 'number',
            'index'        => 'transaction_id',
            'filter_index' => 'main_table.transaction_id',
            'width'        => '50px',
        ));

        $this->addColumn('name', array(
            'header'       => Mage::helper('credit')->__('Customer Name'),
            'index'        => 'name',
            'filter_index' => 'customer_firstname.value'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('credit')->__('Customer Email'),
            'index'  => 'email',
        ));

        $this->addColumn('updated_at', array(
            'header'       => Mage::helper('credit')->__('Date'),
            'index'        => 'updated_at',
            'filter_index' => 'main_table.updated_at',
            'type'         => 'datetime',
        ));

        $this->addColumn('balance_delta', array(
            'header'         => Mage::helper('credit')->__('Balance Change'),
            'index'          => 'balance_delta',
            'type'           => 'currency',
            'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => array(Mage::helper('credit/renderer'), 'amountDelta'),
        ));

        $this->addColumn('balance_amount', array(
            'header'         => Mage::helper('credit')->__('Balance'),
            'index'          => 'balance_amount',
            'type'           => 'currency',
            'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => array(Mage::helper('credit/renderer'), 'amount'),
        ));

        $this->addColumn('action', array(
            'type'         => 'options',
            'header'       => Mage::helper('credit')->__('Action'),
            'index'        => 'action',
            'filter_index' => 'main_table.action',
            'options'      => Mage::getSingleton('credit/system_config_source_action')->toOptionArray()
        ));

        $this->addColumn('message', array(
            'header'         => Mage::helper('credit')->__('Additional Message'),
            'index'          => 'message',
            'filter_index'   => 'main_table.message',
            'frame_callback' => array(Mage::helper('credit/renderer'), 'transactionMessage'),
        ));

        $this->addColumn('is_notified', array(
            'header'       => Mage::helper('credit')->__('Is Notified?'),
            'index'        => 'is_notified',
            'filter_index' => 'main_table.is_notified',
            'width'        => '60px',
            'type'         => 'options',
            'options'      => array(
                1 => Mage::helper('credit')->__('Yes'),
                0 => Mage::helper('credit')->__('No')
            ),
        ));

        return parent::_prepareColumns();
    }
}
