<?php
/**
 * Metizsoft_Taxgenerate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Metizsoft
 * @package        Metizsoft_Taxgenerate
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Countytax admin grid block
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Metizsoft Solutions
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('countytaxGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Grid
     * @author Metizsoft Solutions
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('metizsoft_taxgenerate/countytax')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Grid
     * @author Metizsoft Solutions
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('metizsoft_taxgenerate')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        
        $this->addColumn(
            'state',
            array(
                'header' => Mage::helper('metizsoft_taxgenerate')->__('State Name'),
                'index'  => 'state',
                'type'  => 'options',
                'options' => Mage::helper('metizsoft_taxgenerate')->convertOptions(
                    Mage::getModel('metizsoft_taxgenerate/countytax_attribute_source_state')->getAllOptions(false)
                )
            )
        );
        
        $this->addColumn(
            'county_id',
            array(
                'header'    => Mage::helper('metizsoft_taxgenerate')->__('County'),
                'align'     => 'left',
                'index'     => 'county_id',
                'type'  => 'options',
                'options' => Mage::helper('metizsoft_taxgenerate')->convertOptions(
                    Mage::getModel('metizsoft_taxgenerate/citylist_attribute_source_county')->getAllOptions(false)
                )
            )
        );
        
        $this->addColumn(
            'category_id',
            array(
                'header' => Mage::helper('metizsoft_taxgenerate')->__('Category'),
                'index'  => 'category_id',
                'type'  => 'options',
                'options' => Mage::helper('metizsoft_taxgenerate')->convertOptions(
                    Mage::getModel('metizsoft_taxgenerate/statetax_attribute_source_category')->getAllOptions(false)
                )
            )
        );
        
        
        $this->addColumn(
            'taxtype',
            array(
                'header'  => Mage::helper('metizsoft_taxgenerate')->__('Tax type'),
                'index'   => 'taxtype',
                'type'    => 'options',
                'options' => array(
                    'fix' => Mage::helper('metizsoft_taxgenerate')->__('Fix Amount'),
                    'per' => Mage::helper('metizsoft_taxgenerate')->__('Percentage'),
                )
            )
        );
        
        $this->addColumn(
            'county_tax',
            array(
                'header'    => Mage::helper('metizsoft_taxgenerate')->__('County Tax Amt'),
                'index'     => 'county_tax',
                'align'     => 'left',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('metizsoft_taxgenerate')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('metizsoft_taxgenerate')->__('Enabled'),
                    '0' => Mage::helper('metizsoft_taxgenerate')->__('Disabled'),
                )
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('metizsoft_taxgenerate')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
        /*$this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('metizsoft_taxgenerate')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('metizsoft_taxgenerate')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );*/
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('metizsoft_taxgenerate')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('metizsoft_taxgenerate')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('metizsoft_taxgenerate')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('metizsoft_taxgenerate')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('metizsoft_taxgenerate')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Grid
     * @author Metizsoft Solutions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('countytax');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('metizsoft_taxgenerate')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('metizsoft_taxgenerate')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('metizsoft_taxgenerate')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('metizsoft_taxgenerate')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('metizsoft_taxgenerate')->__('Enabled'),
                            '0' => Mage::helper('metizsoft_taxgenerate')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'state',
            array(
                'label'      => Mage::helper('metizsoft_taxgenerate')->__('Change State'),
                'url'        => $this->getUrl('*/*/massState', array('_current'=>true)),
                'additional' => array(
                    'flag_state' => array(
                        'name'   => 'flag_state',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('metizsoft_taxgenerate')->__('State'),
                        'values' => Mage::getModel('metizsoft_taxgenerate/countytax_attribute_source_state')
                            ->getAllOptions(true),

                    )
                )
            )
        );
        
        $dir = new DirectoryIterator(Mage::getBaseDir() . DS . 'var' . DS . 'import' . DS);
        $extensions = "csv";
        $csv = array();

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile() && stristr($extensions, $fileinfo->getExtension())) {
                $csv[$fileinfo->getFilename()] = $fileinfo->getFilename();
            }               
        }
        $csv = array_merge(array(''=>'Please select'),$csv);
        $this->getMassactionBlock()->addItem('import', array(
            'label'        => Mage::helper('metizsoft_taxgenerate')->__('Import'),
            'url'          => $this->getUrl('*/*/massCsv', array('_current'=>true)),
            'confirm'  => Mage::helper('metizsoft_taxgenerate')->__('Are you sure?'),
            'additional'   => array(
                'subgroup'    => array(
                    'name'     => 'csv',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('customer')->__('Select Csv'),
                    'values'   => $csv
                )
            )
        ));
        
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Metizsoft_Taxgenerate_Model_Countytax
     * @return string
     * @author Metizsoft Solutions
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Metizsoft Solutions
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Grid
     * @author Metizsoft Solutions
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * filter store column
     *
     * @access protected
     * @param Metizsoft_Taxgenerate_Model_Resource_Countytax_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Grid
     * @author Metizsoft Solutions
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
