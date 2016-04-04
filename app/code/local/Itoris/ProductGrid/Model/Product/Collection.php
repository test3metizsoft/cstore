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

class Itoris_ProductGrid_Model_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection {

	public function _loadAttributes($printQuery = false, $logQuery = false) {
		if (empty($this->_items) || empty($this->_itemsById) || empty($this->_selectAttributes)) {
			return $this;
		}

		$entity = $this->getEntity();

		$tableAttributes = array();
		$attributeTypes  = array();
		foreach ($this->_selectAttributes as $attributeCode => $attributeId) {
			if (!$attributeId) {
				continue;
			}
			$attribute = Mage::getSingleton('eav/config')->getCollectionAttribute($entity->getType(), $attributeCode);
			if ($attribute && !$attribute->isStatic()) {
				$tableAttributes[$attribute->getBackendTable()][] = $attributeId;
				if (!isset($attributeTypes[$attribute->getBackendTable()])) {
					$attributeTypes[$attribute->getBackendTable()] = $attribute->getBackendType();
				}
			}
		}
		$useOldWay = !method_exists('Mage', 'getResourceHelper');
		$selects = array();
		foreach ($tableAttributes as $table=>$attributes) {
			$select = $this->_getLoadAttributesSelect($table, $attributes);
			if ($useOldWay) {
				$selects[] = $this->_getLoadAttributesSelect($table, $attributes);
			} else {
				$selects[$attributeTypes[$table]][] = $this->_addLoadAttributesSelectValues(
					$select,
					$table,
					$attributeTypes[$table]
				);
			}
		}
		if ($useOldWay) {
			$this->loadAttributesPrepareSelects($selects);
		} else {
			$selectGroups = Mage::getResourceHelper('eav')->getLoadAttributesSelectGroups($selects);
			foreach ($selectGroups as $selects) {
				if (!empty($selects)) {
					try {
						$select = implode(' UNION ALL ', $selects);
						$values = $this->getConnection()->fetchAll($select);
					} catch (Exception $e) {
						Mage::printException($e, $select);
						$this->printLogQuery(true, true, $select);
						throw $e;
					}

					foreach ($values as $value) {
						$this->_setItemAttributeValue($value);
					}
				}
			}
		}

		return $this;
	}

	protected function loadAttributesPrepareSelects($selects) {
		if (!empty($selects)) {
			try {
				$select = implode(' UNION ', $selects);
				$values = $this->_fetchAll($select);
			} catch (Exception $e) {
				Mage::printException($e, $select);
				$this->printLogQuery(true, true, $select);
				throw $e;
			}

			foreach ($values as $value) {
				$this->_setItemAttributeValue($value);
			}
		}
		return $this;
	}

	public function addCategoryIdsToResult() {
		$productCategories = array();
		foreach ($this as $product) {
			$productCategories[$product->getId()] = array();
		}

		if (!empty($productCategories)) {
			$select = $this->getConnection()->select()
				->from(array('product_category' => $this->getResource()->getTable('catalog/category_product')))
				->where('product_category.product_id IN (?)', array_keys($productCategories));

			$data = $this->getConnection()->fetchAll($select);
			foreach ($data as $row) {
				$productCategories[$row['product_id']][] = $row['category_id'];
			}
		}

		foreach ($this as $product) {
			if (isset($productCategories[$product->getId()])) {
				$product->setData('categories', $productCategories[$product->getId()]);
			}
		}
		return $this;
	}

	protected function _addUrlRewrite() {
		$urlRewrites = null;
		if ($this->_cacheConf) {
			if (!($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'] . 'urlrewrite'))) {
				$urlRewrites = null;
			} else {
				$urlRewrites = unserialize($urlRewrites);
			}
		}

		if (!$urlRewrites) {
			$productIds = array();
			foreach($this->getItems() as $item) {
				$productIds[] = $item->getEntityId();
			}
			if (!count($productIds)) {
				return;
			}
			$storeId = Mage::app()->getRequest()->getParam('store');
			if (!$storeId) {
				$storeId = Mage::app()->getDefaultStoreView()->getId();
			}
			$select = $this->getConnection()->select()
				->from($this->getTable('core/url_rewrite'), array('product_id', 'request_path'))
				->where('store_id = ?', $storeId)
				->where('is_system = ?', 1)
				->where('category_id = ? OR category_id IS NULL', $this->_urlRewriteCategory)
				->where('product_id IN(?)', $productIds)
				->order('category_id ' . self::SORT_ORDER_DESC); // more priority is data with category id
			$urlRewrites = array();

			foreach ($this->getConnection()->fetchAll($select) as $row) {
				if (!isset($urlRewrites[$row['product_id']])) {
					$urlRewrites[$row['product_id']] = $row['request_path'];
				}
			}

			if ($this->_cacheConf) {
				Mage::app()->saveCache(
					serialize($urlRewrites),
					$this->_cacheConf['prefix'] . 'urlrewrite',
					array_merge($this->_cacheConf['tags'], array(Mage_Catalog_Model_Product_Url::CACHE_TAG)),
					$this->_cacheLifetime
				);
			}
		}

		foreach($this->getItems() as $item) {
			if (isset($urlRewrites[$item->getEntityId()])) {
				$item->setData('request_path', $urlRewrites[$item->getEntityId()]);
			} else {
				$item->setData('request_path', false);
			}
		}
	}

	protected function _beforeLoad() {
		if ($this->getStoreId()) {
			/** @var $gridHelper Itoris_ProductGrid_Helper_Grid */
			$gridHelper = Mage::helper('itoris_productgrid/grid');
			$columns = array();
			foreach ($this->_joinAttributes as $attributeCode => $config) {
				/** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
				$attribute = $this->_joinAttributes[$attributeCode]['attribute'];
				if (!$attribute->isScopeGlobal()) {
					$gridHelper->addScopeAttribute($attributeCode);
					if (isset($this->_joinFields[$attributeCode])) {
						$parts = explode('.', $this->_joinFields[$attributeCode]['field']);
						$columns[$attributeCode . '_scope_id'] = $parts[0] . '.store_id';
					}
				}
			}
			if (!empty($columns)) {
				$this->getSelect()->columns($columns);
			}
		}
		return $this;
	}
}
?>