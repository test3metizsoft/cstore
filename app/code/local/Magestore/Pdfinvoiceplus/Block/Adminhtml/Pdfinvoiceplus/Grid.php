<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Pdfinvoiceplus Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('pdfinvoiceplusGrid');
        $this->setDefaultSort('pdfinvoiceplus_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('template_id', array(
            'header' => Mage::helper('pdfinvoiceplus')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'type' => 'number',
            'index' => 'template_id',
        ));

        $this->addColumn('template_name', array(
            'header' => Mage::helper('pdfinvoiceplus')->__('Name'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'template_name',
        ));
        if(Mage::helper('pdfinvoiceplus')->useMultistore()){
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addColumn('stores', array(
                    'header' => Mage::helper('pdfinvoiceplus')->__('Store View'),
                    'index' => 'stores',
                    'type' => 'store',
                    'store_view' => true,
                    'width' => '200px',
                    'sortable' => true,
                    'renderer'  => 'pdfinvoiceplus/adminhtml_pdfinvoiceplus_renderer_stores',
                    'filter_condition_callback' => array($this,
                        '_filterStoreCondition'),
                ));
            }
        }

        $this->addColumn('status', array(
            'header' => Mage::helper('pdfinvoiceplus')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Active',
                2 => 'Inactive',
            ),
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('pdfinvoiceplus')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('pdfinvoiceplus')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('pdfinvoiceplus')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('pdfinvoiceplus')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column) {
        $value = $column->getFilter()->getValue();
        if (!is_null(@$value)) {
            $collection->getSelect()->where('stores like ?', '%'.$value.'%');
        }
        return $this;
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('pdfinvoiceplus_id');
        $this->getMassactionBlock()->setFormFieldName('pdfinvoiceplus');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('pdfinvoiceplus')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('pdfinvoiceplus/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('pdfinvoiceplus')->__('Status'),
                    'values' => $statuses
            ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}