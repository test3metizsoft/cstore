<?php

class Metizsoft_Reports_Adminhtml_Metizsoft_ReportsController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize titles and navigation breadcrumbs
     * @return Metizsoft_Reports_Adminhtml_ReportsController
     */
    protected function _initAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Metizsoft Custom Reports'));
        $this->loadLayout()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('metizsoft_reports')->__('Reports'), Mage::helper('metizsoft_reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('metizsoft_reports')->__('Sales'), Mage::helper('metizsoft_reports')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('metizsoft_reports')->__('Metizsoft Custom Reports'), Mage::helper('metizsoft_reports')->__('Metizsoft Custom Reports'));
        return $this;
    }

    /**
     * Prepare blocks with request data from our filter form
     * @return Metizsoft_Reports_Adminhtml_ReportsController
     */
    protected function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }
 
        $requestData    = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData    = $this->_filterDates($requestData, array('from', 'to','state','county','city'));
       // echo "<pre>";print_r($requestData);die();
        
        $params         = $this->_getDefaultFilterData();
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
 
        foreach ($blocks as $block) {
            if ($block) {
                $block->setFilterData($params);
            }
        }
        return $this;
    }

    /**
     * Grid action Resetalldata
     */
    public function ResetalldataAction()
    {
        $controller = $this->getRequest()->getParam('report');
        $controller = str_replace('/', '', $controller);
        
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read'); 
        $tableName = $resource->getTableName('custom_reports');
        $resource->query("truncate table " . $tableName);
        Mage::getSingleton('core/session')->addSuccess('Successfully Reset all data!!!');
        $this->_redirect('*/*/'.$controller);
    }
    
    /**
     * Grid action
     */
    public function CityreportAction()
    {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_cityreport.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
    
    /**
     * Grid action
     */
    public function countyreportAction()
    {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_countyreport.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
    
    /**
     * state Grid action
     */
    public function statereportAction()
    {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_statereport.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
    /**
     * city Grid action
     */
    
    public function cityAction()
    {
        $this->_initAction();
        $gridBlock = $this->getLayout()->getBlock('adminhtml_reportcity.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
    

    /**
     * Export reports to CSV file
     */
    public function exportcountyCsvAction()
    {
        $fileName   = 'county.csv';
        $grid       = $this->getLayout()->createBlock('metizsoft_reports/adminhtml_countyreport_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
     public function exportstateCsvAction()
    {
        $fileName   = 'state.csv';
        $grid       = $this->getLayout()->createBlock('metizsoft_reports/adminhtml_statereport_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
     public function exportcityCsvAction()
    {
        $fileName   = 'city.csv';
        $grid       = $this->getLayout()->createBlock('metizsoft_reports/adminhtml_cityreport_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }


    /**
     * Export reports to Excel XML file
     */
    public function exportcountyExcelAction()
    {
        $fileName   = 'county.xml';
        $grid       = $this->getLayout()->createBlock('metizsoft_reports/adminhtml_countyreport_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
     public function exportstateExcelAction()
    {
        $fileName   = 'state.xml';
        $grid       = $this->getLayout()->createBlock('metizsoft_reports/adminhtml_statereport_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
     public function exportcityExcelAction()
    {
        $fileName   = 'city.xml';
        $grid       = $this->getLayout()->createBlock('metizsoft_reports/adminhtml_cityreport_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    /**
     * Returns default filter data
     * @return Varien_Object
     */
    protected function _getDefaultFilterData()
    {
        return new Varien_Object(array(
            'from'      => '',
            'to'        => '',
            'state'     =>'',
            'county'    =>'',
            'city'      =>'',
            
        ));
    }
}