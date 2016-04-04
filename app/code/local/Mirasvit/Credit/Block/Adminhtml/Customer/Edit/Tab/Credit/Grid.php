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



class Mirasvit_Credit_Block_Adminhtml_Customer_Edit_Tab_Credit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('desc');

        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setDefaultLimit(100);

        $this->setEmptyText(Mage::helper('credit')->__('No Transactions Found'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('credit/transaction')->getCollection()
            ->addFilterByCustomer(Mage::registry('current_customer')->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('transaction_id', array(
            'header'   => Mage::helper('credit')->__('Transaction #'),
            'type'     => 'number',
            'index'    => 'transaction_id',
            'width'    => '50px',
            'sortable' => false,
        ));

        $this->addColumn('updated_at', array(
            'header'   => Mage::helper('credit')->__('Date'),
            'index'    => 'updated_at',
            'type'     => 'datetime',
            'sortable' => false,
        ));

        $this->addColumn('balance_delta', array(
            'header'         => Mage::helper('credit')->__('Balance Change'),
            'index'          => 'balance_delta',
            'type'           => 'currency',
            'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => array(Mage::helper('credit/renderer'), 'amountDelta'),
            'sortable'       => false,
        ));

        $this->addColumn('balance_amount', array(
            'header'         => Mage::helper('credit')->__('Balance'),
            'index'          => 'balance_amount',
            'type'           => 'currency',
            'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => array(Mage::helper('credit/renderer'), 'amount'),
            'sortable'       => false,
        ));

        $this->addColumn('action', array(
            'header'       => Mage::helper('credit')->__('Action'),
            'index'        => 'action',
            'filter_index' => 'main_table.action',
            'sortable'     => false,
        ));

        $this->addColumn('message', array(
            'header'         => Mage::helper('credit')->__('Additional Message'),
            'index'          => 'message',
            'filter_index'   => 'main_table.message',
            'sortable'       => false,
            'frame_callback' => array(Mage::helper('credit/renderer'), 'transactionMessage'),
        ));

        return parent::_prepareColumns();
    }
}
