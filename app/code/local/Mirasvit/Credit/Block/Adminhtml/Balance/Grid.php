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



class Mirasvit_Credit_Block_Adminhtml_Balance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('balance_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('credit/balance')
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
            'header'       => Mage::helper('credit')->__('Customer Name'),
            'index'        => 'name',
            'filter_index' => 'customer_firstname.value'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('credit')->__('Customer Email'),
            'index'  => 'email',
        ));

        $this->addColumn('amount', array(
            'header'         => Mage::helper('credit')->__('Balance'),
            'index'          => 'amount',
            'type'           => 'currency',
            'currency_code'  => Mage::app()->getStore()->getBaseCurrencyCode(),
            'frame_callback' => array(Mage::helper('credit/renderer'), 'amount'),
        ));

        $this->addColumn('updated_at', array(
            'header' => Mage::helper('credit')->__('Updated At'),
            'index'  => 'updated_at',
            'type'   => 'datetime',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));
    }
}
