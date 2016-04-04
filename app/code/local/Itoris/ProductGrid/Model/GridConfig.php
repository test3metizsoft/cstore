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

class Itoris_ProductGrid_Model_GridConfig extends Varien_Object {

	/** @var Mage_Core_Model_Resource */
	private $resource;
	/** @var Varien_Db_Adapter_Pdo_Mysql */
	private $dbAdapter;

	protected $tableConfig = 'itoris_productgrid_gridconfig';

	protected $storeId = 0;
	protected $loadedForAdmin = false;
	protected $useNextScope = false;
	protected $config = array(
		'default' => array(
			'options' => array(
				'show_scope' => 1,
				'enable_filter' => 1,
				'allow_actions' => 1,
				'allow_bulk_apply' => 1,
				'allow_inline_edit' => 1,
				'allow_reorder' => 1,
				'default_sorting' => 'entity_id',
				'sorting_order' => 'DESC',
				'show_action_column' => 1,
			),
			'attributes' => array(
				'entity_id'        => 1,
				'categories'       => 1,
				'qty'              => 1,
				'websites'         => 1,
				'image'            => 1,
				'name'             => 1,
				'sku'              => 1,
				'price'            => 1,
				'status'           => 1,
				'visibility'       => 1,
			),
		),
		'all_stores' => array(
			'options' => array(),
			'attributes' => array(),
		),
		'store' => array(
			'options' => array(),
			'attributes' => array(),
		),
	);
	protected $orders = array(
		'default' => array(
			'massaction_column_order' => 1,
			'entity_id'        => 2,
			'image'            => 3,
			'name'             => 4,
			'categories'       => 5,
			'sku'              => 6,
			'price'            => 7,
			'qty'              => 8,
			'status'           => 9,
			'visibility'       => 10,
			'websites'         => 11,
			'action_column_order' => 12,
		),
		'all_stores' => array(),
		'store' => array(),
	);

	protected $customColumns = array(
		'preview_link',
	);

	protected $textOptions = array('default_sorting', 'sorting_order');

	public function __construct() {
		$this->resource = Mage::getSingleton('core/resource');
		$this->dbAdapter = $this->resource->getConnection('core_write');
		$this->tableConfig = $this->resource->getTableName($this->tableConfig);
	}

	public function save($settings, $storeId = 0) {
		$this->storeId = (int)$storeId;
		$this->load($this->storeId);
		$newColumnOrders = $this->_prepareColumnOrdersBySettings($settings);

		$this->delete();
		$adminId = (int)$this->getCurrentAdminId();
		//add flag for current store
		$newSettings = array(
			'(\'store_setting\', 1,0,null,' . $this->storeId . ', ' . $adminId . ')'
		);
		$allowMassaction = false;
		if (isset($settings['options'])) {
			$allowMassaction = isset($settings['options']['allow_actions']) && $settings['options']['allow_actions'];
			foreach ($settings['options'] as $code => $value) {
				$orderValue = 'null';
				if (in_array($code, $this->customColumns)) {
					$orderValue = $newColumnOrders[$code];
				}
				$newSettings[] = '(' . $this->dbAdapter->quote($code) . ', ' . $this->dbAdapter->quote($value) . ',0,'. $orderValue .',' . $this->storeId . ', ' . $adminId .')';
			}
		}
		if ($allowMassaction) {
			$newSettings[] = '(' . $this->dbAdapter->quote('massaction_column_order') . ',1,0,'. $newColumnOrders['massaction_column_order'] .',' . $this->storeId . ', ' . $adminId .')';
		}
		$newSettings[] = '(' . $this->dbAdapter->quote('action_column_order') . ',1,0,'. $newColumnOrders['action_column_order'] .',' . $this->storeId . ', ' . $adminId .')';
		if (isset($settings['attributes'])) {
			foreach ($settings['attributes'] as $code => $value) {
				$newSettings[] = '(' . $this->dbAdapter->quote($code) . ', ' . $this->dbAdapter->quote($value) . ',1,' . $newColumnOrders[$code] .',' . $this->storeId . ', ' . $adminId . ')';
			}
		}

		$newSettingsStr = implode(',', $newSettings);
		$this->dbAdapter->query("insert into {$this->tableConfig} (`code`, `value`, `is_attribute`, `column_order`, `store_id`, `admin_id`) values {$newSettingsStr}");

		return $this;
	}

	protected function _prepareColumnOrdersBySettings($settings) {
		$newColumnOrders = array();
		$allColumns = array('action_column_order');
		if (isset($settings['options'])) {
			foreach ($settings['options'] as $code => $value) {
				if ($value) {
					if (in_array($code, $this->customColumns)) {
						$allColumns[] = $code;
					} elseif ($code == 'allow_actions') {
						$allColumns[] = 'massaction_column_order';
					}
				}
			}
		}
		if (isset($settings['attributes'])) {
			foreach ($settings['attributes'] as $code => $value) {
				if ($value) {
					$allColumns[] = $code;
				}
			}
		}
		$columnOrders = $this->getColumnOrders($this->storeId);
		asort($columnOrders);
		$orderValue = 0;
		foreach ($columnOrders as $code => $orderValue) {
			if (in_array($code, $allColumns)) {
				$newColumnOrders[$code] = ++$orderValue;
			}
		}
		$newColumns = array_diff($allColumns, array_keys($columnOrders));
		foreach ($newColumns as $code) {
			$newColumnOrders[$code] = ++$orderValue;
		}


		return $newColumnOrders;
	}

	public function saveOrderValue($code, $order, $isAttribute, $storeId = 0) {
		if ($this->hasEntriesInDb($storeId)) {
			$code = $this->dbAdapter->quote($code);
			$order = intval($order);
			$storeId = $this->hasEntriesInDb($storeId) ? intval($storeId) : 0;
			$isAttribute = $isAttribute ? 1 : 0;
			$adminId = $this->getCurrentAdminId();
			$valuesStr = "({$code},1,{$isAttribute},{$order},{$storeId},{$adminId})";
			$this->dbAdapter->query("replace into {$this->tableConfig} (`code`, `value`, `is_attribute`, `column_order`, `store_id`, `admin_id`) values {$valuesStr}");
		}
		return $this;
	}

	public function load($storeId = 0) {
		$storeId = (int)$storeId;
		$adminId = $this->getCurrentAdminId();
		$data = $this->dbAdapter->fetchAll("
			select * from {$this->tableConfig}
			where (`store_id` = 0 or `store_id` = {$storeId}) and (admin_id is null or admin_id = {$adminId})
		");

		$settings = array();
		foreach ($data as $row) {
			if ($row['admin_id']) {
				$settings[] = $row;
			}
		}
		if (empty($settings)) {
			$settings = $data;
		} else {
			$this->loadedForAdmin = true;
		}

		foreach ($settings as $setting) {
			$scope = (int)$setting['store_id'] ? 'store' : 'all_stores';
			$type = (int)$setting['is_attribute'] ? 'attributes' : 'options';
			$this->config[$scope][$type][$setting['code']] = $setting['value'];
			if ($type == 'attributes'
				|| $setting['code'] == 'action_column_order'
				|| $setting['code'] == 'massaction_column_order'
				|| in_array($setting['code'], $this->customColumns)
			) {
				$this->orders[$scope][$setting['code']] = $setting['column_order'];
			}
		}

		return $this;
	}

	protected function delete() {
		$adminId = (int)$this->getCurrentAdminId();
		$this->dbAdapter->query("delete from {$this->tableConfig} where `store_id` = {$this->storeId} and admin_id = {$adminId}");
		return $this;
	}

	protected function deleteOrders() {
		$adminId = (int)$this->getCurrentAdminId();
		$this->dbAdapter->query("delete from {$this->tableConfig} where `store_id` = {$this->storeId} and admin_id = {$adminId} and column_order is not null");
		return $this;
	}

	public function getOption($code, $isCheckbox = true) {
		return $this->getValue($code, 'options', $isCheckbox);
	}

	public function getAttribute($code) {
		return $this->getValue($code, 'attributes');
	}

	protected function getValue($code, $type, $isCheckbox = true) {
		if ($isCheckbox) {
			if (isset($this->config['store']['options']['store_setting'])) {
				return $this->getValueForScope($code, $type, 'store');
			} elseif (isset($this->config['all_stores']['options']['store_setting'])) {
				return $this->getValueForScope($code, $type, 'all_stores');
			} elseif (isset($this->config['default'][$type][$code])) {
				return $this->config['default'][$type][$code];
			}
		} else {
			if (isset($this->config['store'][$type][$code])) {
				return $this->config['store'][$type][$code];
			} elseif (isset($this->config['all_stores'][$type][$code])) {
				return $this->config['all_stores'][$type][$code];
			} elseif (isset($this->config['default'][$type][$code])) {
				return $this->config['default'][$type][$code];
			}
		}

		return null;
	}

	protected function getValueForScope($code, $type, $scope) {
		if (isset($this->config[$scope][$type][$code])) {
			return $this->config[$scope][$type][$code];
		}

		return null;
	}

	public function getAllAttributes() {
		if (isset($this->config['store']['options']['store_setting'])) {
			return array_keys($this->config['store']['attributes']);
		} elseif (isset($this->config['all_stores']['options']['store_setting'])) {
			return array_keys($this->config['all_stores']['attributes']);
		}

		return array_keys($this->config['default']['attributes']);
	}

	public function getAllAttributesWithOrder() {
		$attributes = $this->getAllAttributes();
		$result = array();
		$orders = $this->getColumnOrders();
		foreach ($attributes as $attribute) {
			$order = isset($orders[$attribute]) ? $orders[$attribute] : null;
			$result[$attribute] = array(
				'order' => $order,
			);
		}

		return $result;
	}

	public function _getColumnOrders() {
		if (!empty($this->orders['store'])) {
			return $this->orders['store'];
		} elseif (!empty($this->orders['all_stores'])) {
			return $this->orders['all_stores'];
		}
		return $this->orders['default'];
	}

	public function getColumnOrders() {
		$orders = $this->_getColumnOrders();
		if ($this->getOption('allow_actions')) {
			if (!isset($orders['massaction_column_order'])) {
				foreach ($orders as $key => $orderValue) {
					$orders[$key] = $orderValue + 1;
				}
				$orders['massaction_column_order'] = count($orders) + 1;
			}
		}
		foreach ($this->getCustomColumns() as $code) {
			if ($this->getOption($code) && !isset($orders[$code])) {
				$orders[$code] = count($orders) + 1;
			}
		}

		if (!isset($orders['action_column_order'])) {
			$orders['action_column_order'] = count($orders) + 1;
		}

		return $orders;
	}

	public function getColumnOrder($column) {
		$orders = $this->getColumnOrders();
		return isset($orders[$column]) ? $orders[$column] : null;
	}

	/**
	 * Check if entries saved in db for current store. Called after load
	 *
	 * @return bool
	 */
	protected function hasEntriesInDb($storeId) {
		return $this->loadedForAdmin && (($storeId && isset($this->config['store']['options']['store_setting'])) || (!$storeId && isset($this->config['all_stores']['options']['store_setting'])));
	}

	public function saveColumnsOrder($columns, $storeId) {
		$this->load($storeId);
		if (!$this->hasEntriesInDb($storeId)) {
			$this->save($this->config['default'], $storeId);
		}
		$columnOrders = $this->getColumnOrders();
		$storeId = intval($storeId);
		$this->storeId = $storeId;
		$this->deleteOrders();

		$newSettings = array();
		$orderValue = 1;
		$adminId = $this->getCurrentAdminId();

		foreach ($columns as $code) {
			if (array_key_exists($code, $columnOrders)) {
				$isAttribute = in_array($code, $this->customColumns) || $code == 'action_column_order' || $code == 'massaction_column_order' ? 0 : 1;
				$newSettings[] = '(' . $this->dbAdapter->quote($code) . ', 1,'. $isAttribute .','. $orderValue .',' . $storeId . ', ' . $adminId .')';
				$orderValue++;
			}
		}
		if (count($newSettings)) {
			$newSettingsStr = implode(',', $newSettings);
			$this->dbAdapter->query("insert into {$this->tableConfig} (`code`, `value`, `is_attribute`, `column_order`, `store_id`, `admin_id`) values {$newSettingsStr}");
		}

		return $this;
	}

	public function getDefaultConfig() {
		return $this->config['default'];
	}

	public function getCurrentAdminId() {
		return (int)Mage::getSingleton('admin/session')->getUser()->getId();
	}

	public function getCustomColumns() {
		return $this->customColumns;
	}
}
?>