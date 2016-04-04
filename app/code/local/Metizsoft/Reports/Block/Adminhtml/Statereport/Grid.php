<?php

class Metizsoft_Reports_Block_Adminhtml_Statereport_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    // add vars used by our methods

    /**
     * Grouped class name of used collection by this grid
     * @var string
     */
    protected $_resourceCollectionName = 'metizsoft_reports/report_collection';

    /**
     * List of columns to aggregate by
     * @var array
     */
    protected $_aggregatedColumns;

    /**
     * Basic setup of our grid
     */
    public function __construct() {

        parent::__construct();
        // change behaviour of grid. This time we won't use pager and ajax functions
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        $this->setFilterVisibility(false);
        $this->setDefaultLimit(50);
        // set message for empty result
        $this->setEmptyCellLabel(Mage::helper('metizsoft_reports')->__('No records found.'));

        // set grid ID in adminhtml
        $this->setId('mxReportsGrid');

        // set our grid to obtain totals
        $this->setCountTotals(true);
    }

    // add getters

    /**
     * Returns the resource collection name which we'll apply filters and display results
     * @return string
     */
    public function getResourceCollectionName() {

        return $this->_resourceCollectionName;
    }

    /**
     * Factory method for our resource collection
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getResourceCollection() {
        $resourceCollection = Mage::getResourceModel($this->getResourceCollectionName());
        return $resourceCollection;
    }

    /**
     * Gets the actual used currency code.
     * We will convert every currency value to this currency.
     * @return string
     */
    public function getCurrentCurrencyCode() {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }

    /**
     * Get currency rate, base to given currency
     * @param string|Mage_Directory_Model_Currency $toCurrency currency code
     * @return int
     */
    public function getRate($toCurrency) {
        return Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }

    /**
     * Return totals data
     * Count totals if it's not previously counted and set to retrieve
     * @return Varien_Object
     */
    public function getTotals() {
        $result = parent::getTotals();
        if (!$result && $this->getCountTotals()) {
            $filterData = $this->getFilterData();
            $totalsCollection = $this->getResourceCollection();

            // apply our custom filters on collection
            $this->_addCustomFilter(
                    $totalsCollection, $filterData
            );

            // isTotals is a flag, we will deal with this in the resource collection
            $totalsCollection->isTotals(true);

            // set totals row even if we didn't got a result
            if ($totalsCollection->count() < 1) {
                $this->setTotals(new Varien_Object);
            } else {
                $this->setTotals($totalsCollection->getFirstItem());
            }

            $result = parent::getTotals();
        }

        return $result;
    }

    // prepare columns and collection

    /**
     * Prepare our grid's columns to display
     * @return Metizsoft_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareColumns() {
        // get currency code and currency rate for the currency renderers.
        // our orders could be in different currencies, therefore we should convert the values to the base currency
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);


        $this->addColumn('state', array(
            'header' => Mage::helper('metizsoft_reports')->__('State'),
            'index' => 'state',
            'width' => 200
        ));

        // add base grand total w/ a currency renderer, and add totals
        $this->addColumn('catname', array(
            'header' => Mage::helper('metizsoft_reports')->__('Category'),
            'index' => 'catname',
            'width' => 200
        ));

        $this->addColumn('protaxunit', array(
            'header' => Mage::helper('metizsoft_reports')->__('Tax Unit Collected'),
            'index' => 'protaxunit',
            'decimals' => 2,
            'width' => 200,
            'total' => 'avg'
        ));

        // add our first column, period which represents a date
        $this->addColumn('totalstatetax', array(
            'header' => Mage::helper('metizsoft_reports')->__('Total'),
            'index' => 'totalstatetax',
            'width' => 100,
            'sortable' => true,
            'period_type' => $this->getFilterData()->getPeriodType() // could be day, month or year
        ));


        // add export types
        $this->addExportType('*/*/exportstateCsv', Mage::helper('metizsoft_reports')->__('CSV'));
        $this->addExportType('*/*/exportstateExcel', Mage::helper('metizsoft_reports')->__('MS Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Prepare our collection which we'll display in the grid
     * First, get the resource collection we're dealing with, with our custom filters applied.
     * In case of an export, we're done, otherwise calculate the totals
     * @return Metizsoft_Reports_Block_Adminhtml_Grid
     */
    protected function _prepareCollection() {
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '-1');
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_string($filter)) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if (0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        $data = $this->getFilterData();
        $from = $data['from'] . ' 00:00:00';
        $to = $data['to'] . ' 23:59:59';

        //Get last order id from custom_reports
        $lastorderid = Mage::getModel('metizsoft_reports/mystatetax')->getCollection();
        $lastorderid->addFieldToSelect('order_id');
        $lastid = $lastorderid->getLastItem()->getData();
        $lastid = (isset($lastid['order_id']))?$lastid['order_id']:0;
        
        $OrderProducts = Mage::getModel('sales/order')->getCollection();
        $OrderProducts->addFieldToSelect('*');
        $OrderProducts->addAttributeToFilter('entity_id', array('gt' => $lastid));
        $OrderProducts->addAttributeToFilter('ism', array('eq' => 0));
        $OrderProducts->addFieldToFilter('state', 'complete');
        
        $i = 1;
        $j = 0;
        $orderdata = array();
        foreach ($OrderProducts as $key => $_order):
            //echo '<pre>';print_r($_order->getData());exit;
            foreach ($_order->getAllItems() as $item) {
            
                $qty = $item->getQtyOrdered()-$item->getQtyRefunded();
                $model = Mage::getModel('metizsoft_reports/mystatetax');
                $model->setOrderId($_order->getId());
                $model->setOrderNumber($_order->getRealOrderId());
                $model->setOrderitemid($item->getItemId());
                $model->setCreated($_order->getCreatedAt());
                $model->setState($_order->getShippingAddress()->getRegion());
                $model->setStatecode($_order->getShippingAddress()->getRegionCode());
                $model->setCounty($_order->getShippingAddress()->getStatecounty());
                $model->setCity($_order->getShippingAddress()->getCity());
                $model->setProductname($item->getName());
                $model->setOrderQty($item->getQtyOrdered());
                $model->setOrderRefunded($item->getQtyRefunded());
                $model->setStatetax($qty*$item->getStatetax());
                $model->setCitytax($qty*$item->getCitytax());
                $model->setCountytax($qty*$item->getCountrytax());
                $model->setProductId($item->getProductId());
                $product = $item->getProduct();
                $cats = $product->getCategoryIds();
                $model->setProtaxunit($product->getTaxunit());
                $orderdatacatids = isset($cats[0]) ? $cats[0] : 0;
                $model->setCatId($orderdatacatids);
                $model->setCatname(Mage::getModel('catalog/category')->load($orderdatacatids)->getName());

                $model->save();
                $j = $j + 1;
            }

            $i = $i + 1;
        endforeach;
        
        $resourceCollection = Mage::getModel('metizsoft_reports/mystatetax')->getCollection();
        if ($data['state'] != ''){
            $resourceCollection->addFieldToFilter('statecode', $data['state']);
        }
        //echo $data['state'];exit;
        $resourceCollection->addFieldToFilter('created', array('gteq' => $from));
        $resourceCollection->addFieldToFilter('created', array('lteq' => $to));
        $resourceCollection->getSelect()
                ->columns('SUM(statetax) as totalstatetax, SUM(protaxunit) as protaxunit')
                ->group('catname')
                ->having('SUM(protaxunit) > 0');
        $resourceCollection->getSelect()->group('catname');
        //echo $resourceCollection->getSelect();exit;
        
        $this->_addCustomFilter(
                $resourceCollection, $filterData
        );
        $this->setCollection($resourceCollection);


        // skip totals if we do an export (calling getTotals would be a duplicate, because
        // the export method calls it explicitly)
        if ($this->_isExport) {
            return $this;
        }

        // count totals if needed
        if ($this->getCountTotals()) {
            $this->getTotals();
        }

        return parent::_prepareCollection();
    }

    /**
     * Apply our custom filters on collection
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @param Varien_Object $filterData
     * @return Metizsoft_Reports_Block_Adminhtml_Report_Grid
     */
    protected function _addCustomFilter($collection, $filterData) {
        return $this;
    }

    /**
     * Returns the columns we specified to summarize totals
     * 
     * Collect all columns we added totals to. 
     * The returned array would be ie. 'base_grand_total' => 'sum'
     * @return array
     */
    protected function _getAggregatedColumns() {
        if (!isset($this->_aggregatedColumns) && $this->getColumns()) {
            $this->_aggregatedColumns = array();
            foreach ($this->getColumns() as $column) {
                if ($column->hasTotal()) {
                    $this->_aggregatedColumns[$column->getId()] = $column->getTotal();
                }
            }
        }

        return $this->_aggregatedColumns;
    }

}
