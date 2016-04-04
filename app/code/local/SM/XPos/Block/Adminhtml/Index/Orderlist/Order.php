<?php

class SM_XPos_Block_Adminhtml_Index_Orderlist_Order extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_filterVisibility = false;
    protected $_headersVisibility = true;
    protected $_pagerVisibility = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sm/xpos/widget/grid.phtml');
        $this->setId('sales_order_create_order_grid');
        $this->setRowClickCallback('order.selectOrder.bind(order)');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection()
    {
        $isXman = 0;
        if (Mage::getStoreConfig('xmanager/general/enabled') == 1) {
            /*xmanager has been installed*/
            $isXman = 1;
        }
        if ($isXman == 1) {
            $per = Mage::getModel('xmanager/permission');
            if ($per->getShareOrder() == 1 || $per->getPermission() == '0') {
                $collection = Mage::getResourceModel('sales/order_grid_collection');
                $this->setCollection($collection);
                return parent::_prepareCollection();
            }
            $allow = $per->getAllowAfterAss();
            $ass = $per->getAssigned();
            $isAss = '0';
            foreach ($ass as $as) {
                if ($as != '0') {
                    $isAss = '1';
                }
            }
            if ($allow == '0' && $isAss != '0') {
                return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
            }

            $currentAdminId = $per->getCurrentAdmin();
            $currentAdminId = $currentAdminId['id'];

            $arrAdminId = array();
            $arrAdminId[] = $currentAdminId;
            foreach ($per->getIdReceive() as $id) {
                $arrAdminId[] = $id;
            }

            $collection = Mage::getResourceModel('sales/order_grid_collection');
            $collection->join(array('so' => 'sales/order'), 'main_table.entity_id=so.entity_id', array('admin_id_create' => 'admin_id_create', 'admin_id_edit' => 'admin_id_edit'), null, 'left')
                ->addAttributeToFilter('admin_id_create', array('in' => $arrAdminId));

            $this->setCollection($collection);
            $collection->getSelect()
                ->columns(array('amount' => new Zend_Db_Expr("FORMAT(grand_total, 2)")));
            return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        } else {
            $collection = Mage::getResourceModel('sales/order_grid_collection');
            $collection->getSelect()->joinLeft(
                array('ba' => $collection->getTable('sales/order_address')),
                'main_table.entity_id=ba.parent_id AND address_type = "billing"',
                array('billing_company' => 'company', 'billing_entityname' => 'entityname')
            )->group('main_table.entity_id');
            $this->setCollection($collection);
            $collection->getSelect()
                ->columns(array('amount' => new Zend_Db_Expr("FORMAT(grand_total, 2)")));
            return parent::_prepareCollection();
        }

    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'main_table.increment_id',
            'renderer'=> 'xpos/adminhtml_index_orderlist_renderer_increment',
        ));

        $this->addColumn('billing_company', array(
            'header' => Mage::helper('sales')->__('Company'),
            'index' => 'billing_company',
            'filter' => false
        ));

        $this->addColumn('billing_entityname', array(
            'header' => Mage::helper('sales')->__('Entityname'),
            'index' => 'billing_entityname',
            'filter' => false
        ));

        $this->addColumn('amount', array(
            'header' => Mage::helper('sales')->__('Amount'),
            'type' => 'price',
            'index' => 'amount',
            'filter' => false
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'filter_index' => 'main_table.created_at',
            'filter' => false,
            'width' => '100px',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'filter_index' => 'main_table.status',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $user_limit = $this->getRequest()->getParam('user_limited');
        if($user_limit != NULL) Mage::getSingleton('core/session')->setUserLimit($user_limit);
        $limit  = Mage::getSingleton('core/session')->getUserLimit();
        if($limit==1)
            $this->addColumn('option_name', array(
                'header' => Mage::helper('sales')->__('Quick action'),
                'index' => 'status',
                'type'  => 'options',
                'width' => '70px',
                'filter'    => false,
                'renderer' => 'xpos/adminhtml_index_orderlist_renderer_action',
            ));

        return parent::_prepareColumns();
    }

    /**
     * Deprecated since 1.1.7
     */
    public function getRowId($row)
    {
        return $row->getId();
    }

    public function getRowUrl($row)
    {
        //if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
        //    return $this->getUrl('*/xPos/index', array('order_id' => $row->getId()));
        //}
        return '';
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadBlock', array('block'=>'order_grid'));
    }
    public function getTypeTable()
    {
        return '';
    }

}
