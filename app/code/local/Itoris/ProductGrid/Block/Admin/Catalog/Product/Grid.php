<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_PRODUCTGRID
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class Itoris_ProductGrid_Block_Admin_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid {

	protected $isEnabledAdvancedGrid = false;
	protected $attributesColumnsConfig = null;
	protected $defaultColumnConfigs = array(
		'entity_id' => array(
			'column_id' => 'entity_id',
			'width'     => '50px',
			'type'      => 'number',
			'index'     => 'entity_id',
		),
		'name' => array(
			'column_id' => 'name',
			'index'     => 'name',
			'is_inline_editable' => true,
			'required' => true,
		),
		'type_id' => array(
			'column_id' => 'type',
			'width' => '60px',
			'index' => 'type_id',
			'type'  => 'options',
		),
		'attribute_set_id' => array(
			'column_id' => 'set_name',
			'width' => '100px',
			'index' => 'attribute_set_id',
			'type'  => 'options',
		),
		'sku' => array(
			'column_id' => 'sku',
			'width' => '80px',
			'index' => 'sku',
			'is_inline_editable' => true,
			'required' => true,
		),
		'price' => array(
			'column_id' => 'price',
			'type'  => 'price',
			'index' => 'price',
			'is_inline_editable' => true,
			'required' => true,
		),
		'qty' => array(
			'column_id' => 'qty',
			'width' => '100px',
			'type'  => 'number',
			'index' => 'qty',
			'is_inline_editable' => true,
			'required' => true,
		),
		'visibility' => array(
			'column_id' => 'visibility',
			'width' => '70px',
			'index' => 'visibility',
			'type'  => 'options',
			'is_inline_editable' => true,
			'required' => true,
		),
		'status' => array(
			'column_id' => 'status',
			'width' => '70px',
			'index' => 'status',
			'type'  => 'options',
			'is_inline_editable' => true,
			'required' => true,
		),
		'websites' => array(
			'column_id' => 'websites',
			'width'     => '100px',
			'sortable'  => false,
			'index'     => 'websites',
			'type'      => 'options',
			'is_inline_editable' => true,
			'multiple' => true,
			'required' => true,
		),
		'categories' => array(
			'column_id' => 'categories',
			'renderer'  => 'itoris_productgrid/admin_catalog_product_grid_column_renderer_category',
			'sortable'  => false,
			'type'      => 'options',
			'index'     => 'categories',
			'is_inline_editable' => true,
			'multiple' => true,
			'required' => true,
		),
		'is_in_stock' => array(
			'column_id' => 'is_in_stock',
			'index'     => 'is_in_stock',
			'type'      => 'options',
			'is_inline_editable' => true,
		),
		'created_at' => array(
			'column_id' => 'created_at',
			'index'     => 'created_at',
			'type'      => 'datetime',
			'is_inline_editable' => false,
		),
		'updated_at' => array(
			'column_id' => 'updated_at',
			'index'     => 'updated_at',
			'type'      => 'datetime',
			'is_inline_editable' => false,
		),
	);

	protected $customColumns = array(
		'preview_link' => array(
			'column_id' => 'preview_link',
			'sortable'  => false,
			'filter'     => false,
			'header'     => 'Preview',
			'renderer'   => 'itoris_productgrid/admin_catalog_product_grid_column_renderer_productLink',
		)
	);

	public function __construct() {
		parent::__construct();
		$this->isEnabledAdvancedGrid = $this->getGridHelper()->isEnabled();
		if ($this->isEnabledAdvancedGrid) {
			if (!$this->getGridHelper()->getConfigValue('enable_filter')) {
				$this->_filterVisibility = false;
			}
			//$this->setDefaultSort($this->getGridHelper()->getConfigValue('default_sorting', false));
			//$this->setDefaultDir($this->getGridHelper()->getConfigValue('sorting_order', false));
			$this->prepareDefaultColumnConfigs();
			$this->setRowClickCallback(false);
		}
	}

	public function prepareDefaultColumnConfigs() {
		$this->defaultColumnConfigs['entity_id']['header'] = Mage::helper('catalog')->__('ID');
		$store = $this->_getStore();
		if ($store->getId()) {
			$this->defaultColumnConfigs['name']['header'] = Mage::helper('catalog')->__('Name in %s', $store->getName());
			$this->defaultColumnConfigs['name']['index'] = 'custom_name';
			$this->defaultColumnConfigs['name']['column_id'] = 'custom_name';
		} else {
			$this->defaultColumnConfigs['name']['header'] = Mage::helper('catalog')->__('Name');
		}
		$this->defaultColumnConfigs['type_id']['header'] = Mage::helper('catalog')->__('Type');
		$this->defaultColumnConfigs['type_id']['options'] = Mage::getSingleton('catalog/product_type')->getOptionArray();
		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
			->load()
			->toOptionHash();
		$this->defaultColumnConfigs['attribute_set_id']['header'] = Mage::helper('catalog')->__('Attrib. Set Name');
		$this->defaultColumnConfigs['attribute_set_id']['options'] = $sets;
		$this->defaultColumnConfigs['sku']['header'] = Mage::helper('catalog')->__('SKU');
		$this->defaultColumnConfigs['price']['header'] = Mage::helper('catalog')->__('Price');
		$this->defaultColumnConfigs['price']['currency_code'] = $store->getBaseCurrency()->getCode();
		$this->defaultColumnConfigs['qty']['header'] = Mage::helper('catalog')->__('Qty');
		$this->defaultColumnConfigs['qty']['is_enabled'] = Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory');
		$this->defaultColumnConfigs['visibility']['header'] = Mage::helper('catalog')->__('Visibility');
		$this->defaultColumnConfigs['visibility']['options'] = Mage::getModel('catalog/product_visibility')->getOptionArray();
		$this->defaultColumnConfigs['status']['header'] = Mage::helper('catalog')->__('Status');
		$this->defaultColumnConfigs['status']['options'] = Mage::getSingleton('catalog/product_status')->getOptionArray();
		$this->defaultColumnConfigs['websites']['header'] = Mage::helper('catalog')->__('Websites');
		$this->defaultColumnConfigs['websites']['options'] = Mage::getModel('core/website')->getCollection()->toOptionHash();
		$this->defaultColumnConfigs['websites']['is_enabled'] = !Mage::app()->isSingleStoreMode();
		$this->defaultColumnConfigs['categories']['header'] = $this->__('Categories');
		$this->defaultColumnConfigs['categories']['options'] = $this->getCategoriesOptions();

		$this->defaultColumnConfigs['is_in_stock']['header'] = Mage::helper('catalog')->__('Stock Availability');
		$this->defaultColumnConfigs['is_in_stock']['options'] = array(
			Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK => Mage::helper('cataloginventory')->__('In Stock'),
			Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK => Mage::helper('cataloginventory')->__('Out of Stock'),
		);
		$this->defaultColumnConfigs['is_in_stock']['is_enabled'] = Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory');

		$this->defaultColumnConfigs['created_at']['header'] = $this->__('Creation Date');
		$this->defaultColumnConfigs['updated_at']['header'] = $this->__('Modification Date');
	}

	protected function getCategoriesOptions() {
		$categoriesOptions = array();
		$this->prepareCategoriesOptions($this->_getCategories()->getNodes(), $categoriesOptions);
		return $categoriesOptions;
	}

	protected function _getCategories() {
		$storeId = (int) $this->getRequest()->getParam('store');
		if ($storeId) {
			$store = Mage::app()->getStore($storeId);
			$parent = $store->getRootCategoryId();
		} else {
			$parent = Mage_Catalog_Model_Category::TREE_ROOT_ID;
		}

		$tree = Mage::getResourceModel('catalog/category_tree');
		/* @var $tree Mage_Catalog_Model_Resource_Category_Tree */
		$nodes = $tree->loadNode($parent)
			->loadChildren(0)
			->getChildren();

		$tree->addCollectionData(null, true, $parent, true, false);

		return $nodes;
	}

	protected function prepareCategoriesOptions($nodes, &$options) {
		/** @var $node Varien_Data_Tree_Node */
		foreach ($nodes as $node) {
			if ($node->getName()) {
				$options[$node->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $node->getLevel() - 1) . $node->getName();
			}
			if ($node->hasChildren()) {
				$this->prepareCategoriesOptions($node->getAllChildNodes(), $options);
			}
		}

		return $options;
	}

	/**
	 * Get grid column configs
	 *
	 * @return null|array
	 */
	protected function getAttributesColumns() {
		if (is_null($this->attributesColumnsConfig)) {
			$this->attributesColumnsConfig = $this->getGridHelper()->getAttributesColumns();
		}

		return $this->attributesColumnsConfig;
	}

	protected function _prepareCollection() {
		if ($this->isEnabledAdvancedGrid) {
			$store = $this->_getStore();
			$notAllowedToSelect = array('price', 'status', 'visibility');
			$attributes = $this->getAttributesColumns();
			/** @var $collection Itoris_ProductGrid_Model_Product_Collection */
			$collection = Mage::getModel('itoris_productgrid/product_collection');
			$collection->setStoreId($store->getId());
			foreach ($attributes as $key => $attribute) {
				if (!in_array($key, $notAllowedToSelect)) {
					$collection->addAttributeToSelect($key);
				}
			}

			if (isset($attributes['qty']) && Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
				$collection->joinField('qty',
					'cataloginventory/stock_item',
					'qty',
					'product_id=entity_id',
					'{{table}}.stock_id=1',
					'left');
			}
			if (isset($attributes['is_in_stock']) && Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
				$collection->joinField('is_in_stock',
					'cataloginventory/stock_item',
					'is_in_stock',
					'product_id=entity_id',
					'{{table}}.stock_id=1',
					'left');
			}
			$showPreview = $this->getGridHelper()->getConfigValue('preview_link');
			if ($store->getId()) {
				//$collection->setStoreId($store->getId());
				$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
				$collection->addStoreFilter($store);
				if (isset($attributes['name'])) {
					$collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
					$collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
				}
				if (isset($attributes['status']) || $showPreview) {
					$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
				}
				if (isset($attributes['visibility']) || $showPreview) {
					$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id',	null, 'inner', $store->getId());
				}
				if (isset($attributes['price'])) {
					$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null,	'left', $store->getId());
				}
			} else {
				if (isset($attributes['price'])) {
					$collection->addAttributeToSelect('price');
				}
				if (isset($attributes['status']) || $showPreview) {
					$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
				}
				if (isset($attributes['visibility']) || $showPreview) {
					$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
				}
			}

			$this->setCollection($collection);

			$this->_preparePage();

			$gridAdmin = $this->getGridAdmin();
			$gridAdminChanged = false;

			$columnId = $this->getParam($this->getVarNameSort());
			if ($columnId) {
				if ($gridAdmin->getGridSortColumn() != $columnId) {
					$gridAdmin->setGridSortColumn($columnId);
					$gridAdminChanged = true;
				}
			} else if ($gridAdmin->getGridSortColumn()) {
				$columnId = $gridAdmin->getGridSortColumn();
			} else {
				$columnId = $this->_defaultSort;
			}
			$dir      = $this->getParam($this->getVarNameDir(), $gridAdmin->getGridSortDir());
			if ($dir) {
				if ($gridAdmin->getGridSortDir() != $dir) {
					$dir = strtolower($dir);
					if ($dir != 'asc' && $dir != 'desc') {
						$dir = 'desc';
					}
					$gridAdmin->setGridSortDir($dir);
					$gridAdminChanged = true;
				}
			} else if ($gridAdmin->getGridSortDir()) {
				$dir = $gridAdmin->getGridSortDir();
			} else {
				$dir = $this->_defaultDir;
			}
			$filter   = $this->getParam($this->getVarNameFilter(), null);

			if (is_null($filter)) {
				if ($gridAdmin->getGridFilter()) {
					$filter = $gridAdmin->getGridFilter();
				} else {
					$filter = $this->_defaultFilter;
				}
			} elseif (is_string($filter)) {
				if ($gridAdmin->getGridFilter() != $filter) {
					$gridAdmin->setGridFilter($filter);
					$gridAdminChanged = true;

				}
			}
			if ($gridAdminChanged) {
				$gridAdmin->save();
			}

			if (is_string($filter)) {
				$data = $this->helper('adminhtml')->prepareFilterString($filter);
				$this->_setFilterValues($data);
			} else if ($filter && is_array($filter)) {
				$this->_setFilterValues($filter);
			} else if (0 !== sizeof($this->_defaultFilter)) {
				$this->_setFilterValues($this->_defaultFilter);
			}

			if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
				$dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
				$this->_columns[$columnId]->setDir($dir);
				$column = $this->_columns[$columnId];
				$columnIndex = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
				if ($columnIndex == 'price' && $store->getId()) {
					$this->getCollection()->getSelect()->order($columnIndex.' '.strtoupper($column->getDir()));
				} else {
					if ($columnIndex != 'price' && isset($attributes[$columnIndex]['index']) && $attributes[$columnIndex]['type'] != 'text' && !in_array($columnIndex, array('status', 'visibility'))) {
						$collection->joinAttribute($columnIndex, 'catalog_product/'.$columnIndex, 'entity_id', null, 'left');
						$this->getCollection()->getSelect()->order($columnIndex.' '.strtoupper($column->getDir()));
					} else {
						$this->getCollection()->setOrder($columnIndex, strtoupper($column->getDir()));
					}
				}
			}

			if (!$this->_isExport) {
				$this->getCollection()->load();
				$this->_afterLoadCollection();
			}

			$this->getCollection()->addWebsiteNamesToResult();
			if (isset($attributes['categories'])) {
				$this->getCollection()->addCategoryIdsToResult();
			}

			if ($this->getGridHelper()->getConfigValue('preview_link')) {
				$collection->addUrlRewrite();
			}

			return $this;
		}

		return parent::_prepareCollection();
	}

	protected function _addColumnFilterToCollection($column) {
		if ($this->getCollection()) {
			if ($column->getId() == 'categories') {
				$this->getCollection()->joinField('categories',
					'catalog/category_product',
					'category_id',
					'product_id=entity_id',
					null,
					'left');
			}
		}
		return parent::_addColumnFilterToCollection($column);
	}

	protected function _prepareGrid() {
		parent::_prepareGrid();
		if ($this->isEnabledAdvancedGrid) {
			$this->sortColumnsByOrderValue();
		}

		return $this;
	}

	protected function _prepareColumns() {
		if ($this->isEnabledAdvancedGrid) {
			$store = $this->_getStore();
			$attributes = $this->getAttributesColumns();
			$inlineEditTypes = array('options', 'text', 'date', 'textarea', 'price', 'media_image', 'weight');
			$columnOrders = $this->getGridHelper()->getColumnOrders();
			foreach ($attributes as $key => $attribute) {
				if (isset($columnOrders[$key])) {
					$columnOrder = $columnOrders[$key];
				} else {
					$columnOrder = count($columnOrders) + 1;
				}
				$columnId = null;
				if (isset($this->defaultColumnConfigs[$key])) {
					if (!isset($this->defaultColumnConfigs[$key]['is_enabled']) || $this->defaultColumnConfigs[$key]['is_enabled']) {
						$columnId = $this->defaultColumnConfigs[$key]['column_id'];
						$columnConfig = $this->defaultColumnConfigs[$key];
					}
				} else {
					if (in_array($attribute['type'], $inlineEditTypes)) {
						$attribute['is_inline_editable'] = true;
					}
					if ($attribute['type'] == 'price') {
						$attribute['currency_code'] = $store->getBaseCurrency()->getCode();
					}
					$columnId = $key;
					$columnConfig = $attribute;
				}
				if (!is_null($columnId)) {
					$columnTitle = $columnConfig['header'];
					if ($this->getGridHelper()->getConfigValue('allow_reorder')) {
						$columnConfig['header'] = '<span class="itoris-ceil-title">'.$columnTitle.'</span>';
						$columnConfig['header'] .= '<span style="display:none;" class="itoris-attribute-code">'.$key.'</span>';
					}
					if ($this->getGridHelper()->getConfigValue('allow_inline_edit')) {
						$columnConfig['header'] .= '<span style="display:none;" class="itoris-inline-edit-ceil-title">' . htmlspecialchars($this->__('Click to edit %s', $columnTitle)) . '</span>';
					}
					$columnConfig['order'] = $columnOrder;
					$this->addColumn($columnId, $columnConfig);
				}
			}

			$this->_addCustomColumns();

			$this->_addActionColumn($this->getGridHelper()->getActionColumnOrder($store->getId()));

			if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
				$this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
			}

			return $this;
		}

		return parent::_prepareColumns();
	}

	protected function _addCustomColumns() {
		$columnOrders = $this->getGridHelper()->getColumnOrders();
		foreach ($this->customColumns as $key => $column) {
			if ($this->getGridHelper()->getConfigValue($key)) {
				$column['header'] = $this->__($column['header']) . '<span style="display:none;" class="itoris-attribute-code">' . $key . '</span>';
				$column['order'] = $columnOrders[$key];
				$this->addColumn($key, $column);
			}
		}

		return $this;
	}

	protected function _addActionColumn($columnOrder = 0) {
		if ($this->getGridHelper()->getConfigValue('show_action_column')) {
			$this->addColumn('action',
				array(
					'header'    => Mage::helper('catalog')->__('Action') . '<span style="display:none;" class="itoris-attribute-code">action_column_order</span>',
					'width'     => '50px',
					'type'      => 'action',
					'getter'     => 'getId',
					'actions'   => array(
						array(
							'caption' => Mage::helper('catalog')->__('Edit'),
							'url'     => array(
								'base'=>'adminhtml/catalog_product/edit',
								'params'=>array('store'=>$this->getRequest()->getParam('store'))
							),
							'field'   => 'id'
						)
					),
					'filter'    => false,
					'sortable'  => false,
					'index'     => 'stores',
					'order'     => $columnOrder,
				));
		}
		return $this;
	}

	public function addColumn($columnId, $column) {
		if ($this->isEnabledAdvancedGrid && $this->getGridHelper()->getConfigValue('allow_actions')) {
			if (is_array($column)) {
				$this->_columns[$columnId] = $this->getLayout()->createBlock('itoris_productgrid/admin_catalog_product_grid_column')
					->setData($column)
					->setGrid($this);
			} else {
				throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
			}

			$this->_columns[$columnId]->setId($columnId);
			$this->_lastColumnId = $columnId;
			return $this;
		}

		return parent::addColumn($columnId, $column);
	}

	public function sortColumnsByOrderValue() {
		//use only order value!
		$keys = array();
		foreach ($this->_columns as $key => $column) {
			$keys[$key] = isset($column['order']) ? $column['order'] : 0;
		}
		asort($keys);
		$sorted = array();
		foreach ($keys as $key => $sortOrder) {
			$sorted[$key] = $this->_columns[$key];
		}
		$this->_columns = $sorted;

		end($this->_columns);
		$this->_lastColumnId = key($this->_columns);
		return $this;
	}

	protected function convertForElm($values) {
		$result = array();
		foreach ($values as $key => $label) {
			$result[] = array(
				'value' => $key,
				'label' => $label,
			);
		}

		return $result;
	}

	protected function _prepareMassaction() {
		if ($this->isEnabledAdvancedGrid) {
			if ($this->getGridHelper()->getConfigValue('allow_actions')) {
				parent::_prepareMassaction();
				if ($this->getGridHelper()->getConfigValue('allow_bulk_apply')) {
					$attributes = $this->getGridHelper()->getProductAttributesConfig();
					$attributes = array_merge(array(
						array(
							'value'   => 'category_ids',
							'label'   => $this->__('Categories'),
							'type'    => 'multiselect',
							'required' => 0,
							'options'  => $this->convertForElm($this->getCategoriesOptions()),
						),
						array(
							'value'   => 'qty',
							'label'   => Mage::helper('catalog')->__('Qty'),
							'type'    => 'text',
							'required' => 1,
						),
						array(
							'value'    => 'is_in_stock',
							'label'    => Mage::helper('catalog')->__('Stock Availability'),
							'type'     => 'select',
							'required' => 0,
							'options'  => array(
								Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK => Mage::helper('cataloginventory')->__('In Stock'),
								Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK => Mage::helper('cataloginventory')->__('Out of Stock'),
							),
						),
						array(
							'value'   => 'website_ids',
							'label'   => Mage::helper('catalog')->__('Websites'),
							'type'    => 'multiselect',
							'required' => 0,
							'options' => $this->convertForElm(Mage::getModel('core/website')->getCollection()->toOptionHash()),
						),
					), $attributes);
					$additional = array(
						'attributes' => array(
							'name' => 'attr_id',
							'type' => 'select',
							'class' => 'required-entry',
							'label' => $this->__('Attribute'),
							'values' => array_merge(array(
								array(
									'value' => '',
									'label' => $this->__('Please select attribute'),
								),
							), $attributes),
							'onchange' => 'itorisProductGrid.showAttributeValuesForBulk(this)',
							'after_element_html' => '<script type="text/javascript">itorisProductGridAttributesConfig = '. Zend_Json::encode($attributes) .'</script>'
						),
						'attr_date' => array(
							'name' => 'attr_value',
							'type' => 'date',
							'class' => 'required-entry itoris-bulk-attribute-value',
							'label' => $this->__('Attribute Value'),
							'image' => $this->getSkinUrl('images/grid-cal.gif'),
							'format' => 'M/d/yyyy',
						),
						'attr_text' => array(
							'name' => 'attr_value',
							'type' => 'text',
							'class' => 'required-entry itoris-bulk-attribute-value',
							'label' => $this->__('Attribute Value'),
						),
						'attr_textarea' => array(
							'name' => 'attr_value',
							'type' => 'textarea',
							'class' => 'required-entry itoris-bulk-attribute-value',
							'label' => $this->__('Attribute Value'),
						),
						'attr_price' => array(
							'name' => 'attr_value',
							'type' => 'text',
							'class' => 'required-entry validate-number itoris-bulk-attribute-value',
							'label' => $this->__('Attribute Value'),
						),
					);
					foreach ($attributes as $attribute) {
						if (isset($attribute['options'])) {
							$additional['attr_' . $attribute['value']] = array(
								'name' => 'attr_value',
								'type' => $attribute['type'],
								'class' => 'itoris-bulk-attribute-value' . ($attribute['required'] ? ' validate-select' : ''),
								'label' => $this->__('Attribute Value'),
								'values' => $attribute['options'],
							);
						}
					}
					$additional['attr_multiselect_action'] = array(
						'name' => 'attr_multiselect_action',
						'type' => 'select',
						'label' => $this->__('Apply As'),
						'class' => 'itoris-bulk-attribute-value',
						'values' => array(
							array(
								'value' => 'default',
								'label' => $this->__('Exact Match'),
							),
							array(
								'value' => 'add',
								'label' => $this->__('Add Selected'),
							),
							array(
								'value' => 'remove',
								'label' => $this->__('Remove Selected'),
							),
						),
					);
					$this->getMassactionBlock()->addItem('edit_attribute', array(
						'label'=> $this->__('Attribute Bulk Apply'),
						'url'  => $this->getUrl('itoris_productgrid/admin_grid/massAttribute', array('_current'=>true)),
						'confirm' => $this->__('Do you really want to make changes in all selected products?'),
						'additional' => $additional,
					));
				}
			}
			return $this;
		} else {
			return parent::_prepareMassaction();
		}
	}

	protected function _prepareMassactionColumn() {
		parent::_prepareMassactionColumn();
		if ($this->isEnabledAdvancedGrid) {
			$massActionOrder = $this->getGridHelper()->getMassactionColumnOrder();
			$massActionColumn = $this->_columns['massaction'];
			$massActionColumn->setHeader('<span style="display:none;" class="itoris-attribute-code">massaction_column_order</span>')
				->setData('renderer', 'itoris_productgrid/admin_catalog_product_grid_column_renderer_massaction')
				->setOrder($massActionOrder);
		}
		return $this;
	}

	public function getRowUrl($row) {
		if ($this->isEnabledAdvancedGrid && $this->getGridHelper()->getConfigValue('allow_inline_edit')) {
			return null;
		}
		return parent::getRowUrl($row);
	}

	public function getGridAdmin() {
		$currentStoreId = (int)$this->getRequest()->getParam('store', 0);
		$admin = Mage::getModel('itoris_productgrid/admin');
		$admin->setCurrentStoreId($currentStoreId)
			->load(Mage::getSingleton('admin/session')->getUser()->getId(), 'admin_id');

		if (!$admin->getId()) {
			$admin->setAdminId(Mage::getSingleton('admin/session')->getUser()->getId())
				->setStoreId($currentStoreId);
		}
		if (!$admin->getStoreId() && $currentStoreId) {
			$admin->setId(null)->setStoreId($currentStoreId);
		}

		return $admin;
	}

	/**
	 * @return Itoris_ProductGrid_Helper_Grid
	 */
	protected function getGridHelper() {
		return Mage::helper('itoris_productgrid/grid');
	}


}
?>