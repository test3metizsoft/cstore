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

class Itoris_ProductGrid_Admin_GridController extends Itoris_ProductGrid_Controller_Admin_Controller {

	public function saveConfigAction() {
		if ($this->getRequest()->getParam('itoris_productgrid_flag')) {
			$data = array(
				'options'    => $this->getCodesOfCheckedOptions($this->getRequest()->getParam('general_options', array())),
				'attributes' => $this->getCodesOfCheckedOptions($this->getRequest()->getParam('attributes', array())),
			);
			$storeId = $this->getRequest()->getParam('store', 0);
			try {
				/** @var $gridConfig Itoris_ProductGrid_Model_GridConfig */
				$gridConfig = Mage::getModel('itoris_productgrid/gridConfig');
				$gridConfig->save($data, $storeId);
				$this->_getSession()->addSuccess('Grid configuration has been saved');
			} catch (Exception $e) {
				Mage::logException($e);
				$this->_getSession()->addError('Grid configuration has not been saved');
				$this->_getSession()->addError($e->getMessage());
			}
		}

		$this->_redirect('adminhtml/catalog_product/', array('_current' => true));
	}

	protected function getCodesOfCheckedOptions($data) {
		$codes = array();
		if (is_array($data)) {
			foreach ($data as $code => $value) {
				if (!empty($value)) {
					$codes[$code] = $value;
				}
			}
		}

		return $codes;
	}

	public function massAttributeAction() {
		$productIds = $this->getRequest()->getParam('product');
		$attrId = $this->getRequest()->getParam('attr_id');
		/** @var $attribute Mage_Catalog_Model_Entity_Attribute */
		$attribute = Mage::getModel('catalog/entity_attribute');
		$attributeApplyAs = $this->getRequest()->getParam('attr_multiselect_action');
		try {
			$productSavedCount = 0;
			$attrValue = $this->getRequest()->getParam('attr_value');
			$systemAttr = array('qty', 'category_ids', 'website_ids', 'is_in_stock');
			if (in_array($attrId, $systemAttr)) {
				$attributeLabel = $attrId;
				foreach ($productIds as $id) {
					/** @var $product Mage_Catalog_Model_Product */
					$product = Mage::getModel('catalog/product')->load($id);
					if ($product->getId()) {
						if ($attrId == 'qty' || $attrId == 'is_in_stock') {
							if ($attrId == 'qty' && in_array($product->getTypeId(), array('grouped', 'configurable', 'bundle'))) {
								continue;
							}
							/** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
							$stockItem = $product->getStockItem();
							if ($attrId == 'qty') {
								$stockItem->setQty($attrValue);
							} elseif ($attrId == 'is_in_stock') {
								$stockItem->setIsInStock($attrValue);
							}
							$stockItem->save();
						} else {
							if ($attributeApplyAs == 'add' || $attributeApplyAs == 'remove') {
								if ($attrId == 'category_ids') {
									$defaultValues = $product->getCategoryIds();
								} else {
									$defaultValues = $product->getWebsiteIds();
								}
								$preparedValues = $this->_prepareMultiselectValues($defaultValues, $attrValue, $attributeApplyAs);
								$product->setData($attrId, $preparedValues);
							} else {
								$product->setData($attrId, $attrValue);
							}
							$product->save();
						}
						$productSavedCount++;
					}
				}
			} else if (is_array($productIds) && $attribute->load($attrId)->getId()) {
				$attrValue = $this->prepareAttributeValue($attrValue, $attribute);
				$attributeLabel = $attribute->getFrontendLabel();
				$storeId = (int)$this->getRequest()->getParam('store');
				foreach ($productIds as $id) {
					/** @var $product Mage_Catalog_Model_Product */
					$product = Mage::getModel('catalog/product')->load($id);
					if ($product->getId()) {
						if ($attribute->getAttributeCode() == 'price' && in_array($product->getTypeId(), array('grouped', 'bundle'))) {
							continue;
						}
						if ($storeId) {
							$product->setStoreId($storeId);
						}
						if ($attribute->getAttributeCode() == 'sku') {
							$product->setData($attribute->getAttributeCode(), $attrValue);
							$product->save();
							$productSavedCount++;
						} else {
							if ($product->getResource()->getAttribute($attrId)) {
								$allowedAttributes = Mage::helper('itoris_productgrid/grid')->getAttributesCodeForSet($product->getAttributeSetId());
								if (in_array($attribute->getAttributeCode(), $allowedAttributes)) {
									if ($attribute->getFrontendInput() == 'multiselect' && ($attributeApplyAs == 'add' || $attributeApplyAs == 'remove')) {
										$defaultValues = $product->getData($attribute->getAttributeCode());
										$defaultValues = explode(',', $defaultValues);
										$newValues = explode(',', $attrValue);
										$preparedValues = $this->_prepareMultiselectValues($defaultValues, $newValues, $attributeApplyAs);
										$product->addAttributeUpdate($attribute->getAttributeCode(), implode(',', $preparedValues), $storeId);
									} else {
										$product->addAttributeUpdate($attribute->getAttributeCode(), $attrValue, $storeId);
									}
									$productSavedCount++;
								}
							}
						}
					}
				}
			}
			$this->_getSession()->addSuccess($this->__('Attribute %s has been updated. Products updated: %d', $attributeLabel, $productSavedCount));
		} catch (Exception $e) {
			if ($productSavedCount) {
				$this->_getSession()->addSuccess($this->__('Attribute %s has been updated. Products updated: %d', $attribute->getFrontendLabel(), $productSavedCount));
			} else {
				Mage::logException($e);
				$this->_getSession()->addError('Attribute have not been saved');
			}
			$this->_getSession()->addError($e->getMessage());
		}
		$this->_redirect('adminhtml/catalog_product/', array('_current' => true));
	}

	protected function _prepareMultiselectValues($defValues, $newValues, $type) {
		if ($type == 'add') {
			$newValues = array_merge($defValues, $newValues);
			$newValues = array_unique($newValues);
		} else if ($type == 'remove') {
			foreach ($newValues as $newValue) {
				foreach ($defValues as $key => $defaultValue) {
					if ($defaultValue == $newValue) {
						unset($defValues[$key]);
					}
				}
			}
			$newValues = $defValues;
		}
		$newValues = array_filter($newValues);
		sort($newValues);
		return $newValues;
	}

	protected function prepareAttributeValue($value, $attribute) {
		if (is_array($value)) {
			$value = array_map('trim', $value);
			$value = implode(',', $value);
		} else {
			if (!method_exists('Varien_Date', 'formatDate') && $value && $attribute->getBackendType() == 'datetime') {
				if (!is_numeric($value)) {
					$value = strtotime($value);
				}
				$value = date('Y-m-d H:i:s', $value);
			}
		}
		return $value;
	}

	public function saveColumnOrderAction() {
		$result = array();
		$columns = $this->getRequest()->getPost('columns');
		$columns = explode(',', $columns);
		try {
			$grid = Mage::getModel('itoris_productgrid/gridConfig');
			$grid->saveColumnsOrder($columns, $this->getRequest()->getParam('store', 0));
			$result['ok'] = true;
		} catch (Exception $e) {
			$result['error'] = $e->getMessage();
		}

		return $this->getResponse()->setBody(Zend_Json::encode($result));
	}

	public function saveInlineEditAttrAction() {
		$result = array();
		$productId = $this->getRequest()->getPost('product_id');
		$attrCode = $this->getRequest()->getPost('attr_code');
		$attrValue = $this->getRequest()->getPost('attr_value');
		if ($attrCode == 'websites') {
			$attrCode = 'website_ids';
		} elseif ($attrCode == 'categories') {
			$attrCode = 'category_ids';
		} elseif ($attrCode == 'custom_name') {
			$attrCode = 'name';
		}
		
		$attributes = Mage::helper('itoris_productgrid/grid')->getAttributesColumns();
		//check if unique
		if ($attrValue != "" && isset($attributes[$attrCode]) && $attributes[$attrCode]['type'] == 'text' && !!intval($attributes[$attrCode]['is_unique'])) {
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			if ($attrCode == 'sku') {
				$table = Mage::getSingleton("core/resource")->getTableName('catalog_product_entity');
				$value_id = $connection->fetchOne("select `entity_id` from {$table} where `sku` = '{$attrValue}'");			
			} else {		
				$table = Mage::getSingleton("core/resource")->getTableName('catalog_product_entity_varchar');
				$value_id = $connection->fetchOne("select `value_id` from {$table} where `value` = '{$attrValue}' and attribute_id = ".intval($attributes[$attrCode]['id']));
			}
			if (intval($value_id) > 0) {
				$result['error'] = 'The value of attribute "'.$attrCode.'" must be unique';
				$this->getResponse()->setBody(Zend_Json::encode($result));
				return;
			}
		}

		try {
			$storeId = $this->getRequest()->getParam('store', 0);
			/** @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel('catalog/product')->load($productId);
			if ($storeId) {
				$product->setStoreId($storeId);
			}
			if ($product->getId()) {
				$useDefault = $this->getRequest()->getParam('use_default');
				if ($useDefault) {
					$attrValue = false;
				}
				if ($attrCode == 'qty' || $attrCode == 'is_in_stock') {
					/** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
					$stockItem = $product->getStockItem();
					if ($attrCode == 'qty') {
						$stockItem->setQty($attrValue);
					} elseif ($attrCode == 'is_in_stock') {
						$stockItem->setIsInStock($attrValue);
					}
					$stockItem->save();
				} elseif (!in_array($attrCode, Itoris_ProductGrid_Helper_Grid::$notAttributes)) {
					$attrValue = $this->prepareAttributeValue($attrValue, $product->getResource()->getAttribute($attrCode));
					if ($useDefault) {
						$product->setData($attrCode, $attrValue);
						$product->save();
					} else {
						$product->addAttributeUpdate($attrCode, $attrValue, $storeId);
					}
				} else {
					$product->setData($attrCode, $attrValue);
					$product->save();
				}
				if ($storeId) {//making sure the value for default store is created
					$product->setStoreId(0);
					$product->setData($attrCode, $product->getData($attrCode))->save();
				}
				if ($useDefault) {
					$result['default_value'] = Mage::getModel('catalog/product')->load($productId)->getData($attrCode);
					if ($attrCode == 'price') {
						$result['default_value'] = number_format($result['default_value'], 2);
					}
				}
				//$this->loadLayout();
				//$this->_renderAjaxGrid();
				$result['ok'] = true;
			}
		} catch (Exception $e) {
			$result['error'] = $e->getMessage();
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
	}

	public function massSaveInlineEditAttrAction() {
		$result = array();
		$products = $this->getRequest()->getPost('products');
		$storeId = $this->getRequest()->getParam('store', 0);
		$isUniqError = false;
		$allAttributes = Mage::helper('itoris_productgrid/grid')->getAttributesColumns();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		try {
			if (is_array($products)) {
				foreach ($products as $productId => $attributes) {
					if (is_array($attributes)) {
						/** @var $product Mage_Catalog_Model_Product */
						$product = Mage::getModel('catalog/product')->load($productId);
						if ($product->getId()) {
							if ($storeId) {
								$product->setStoreId($storeId);
							}
							/** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
							$stockItem = $product->getStockItem();
							$saveStockItem = false;
							$saveProduct = false;
							foreach ($attributes as $attrCode => $attrValue) {
								if ($attrCode == 'websites') {
									$attrCode = 'website_ids';
								} elseif ($attrCode == 'categories') {
									$attrCode = 'category_ids';
								} elseif ($attrCode == 'custom_name') {
									$attrCode = 'name';
								}
								
								//check if unique
								if ($attrValue != "" && isset($allAttributes[$attrCode]) && $allAttributes[$attrCode]['type'] == 'text' && !!intval($allAttributes[$attrCode]['is_unique'])) {
									$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
									if ($attrCode == 'sku') {
										$table = Mage::getSingleton("core/resource")->getTableName('catalog_product_entity');
										$value_id = $connection->fetchOne("select `entity_id` from {$table} where `sku` = '{$attrValue}'");			
									} else {		
										$table = Mage::getSingleton("core/resource")->getTableName('catalog_product_entity_varchar');
										$value_id = $connection->fetchOne("select `value_id` from {$table} where `value` = '{$attrValue}' and attribute_id = ".intval($allAttributes[$attrCode]['id']));
									}
									if (intval($value_id) > 0) {
										$isUniqError = true;
										$result['error'] .= 'The value of attribute "'.$attrCode.'" must be unique, product ID = '.$productId."~~";
										continue;
									}
								}
		
								if ($attrCode == 'qty' || $attrCode == 'is_in_stock') {
									if ($attrCode == 'qty') {
										$stockItem->setQty($attrValue);
									} elseif ($attrCode == 'is_in_stock') {
										$stockItem->setIsInStock($attrValue);
									}
									$saveStockItem = true;
								} elseif (!in_array($attrCode, Itoris_ProductGrid_Helper_Grid::$notAttributes)) {
									$attrValue = $this->prepareAttributeValue($attrValue, $product->getResource()->getAttribute($attrCode));
									$product->addAttributeUpdate($attrCode, $attrValue, $storeId);
								} else {
									$product->setData($attrCode, $attrValue);
									$saveProduct = true;
								}
								if ($storeId) {//making sure the value for default store is created
									$product->setStoreId(0);
									$product->setData($attrCode, $product->getData($attrCode))->save();
									$product->setStoreId($storeId);
								}
							}
							if ($saveStockItem) {
								$stockItem->save();
							}
							if ($saveProduct) {
								$product->save();
							}
						}
					}
				}
			}
			$this->loadLayout();
			$this->_renderAjaxGrid();
			if ($isUniqError) echo '<script type="text/javascript">alert(\''.addslashes($result['error']).'\'.replace(/~~/g, "\\n"));</script>';
			return;
		} catch (Exception $e) {
			$result['error'] = $e->getMessage();
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
	}

	public function getGalleryConfigAction() {
		$result = array();
		try {
			$storeId = $this->getRequest()->getParam('store', 0);
			$productId = $this->getRequest()->getParam('product_id');
			/** @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel('catalog/product')->load($productId);
			if ($product->getId()) {
				$product->setStoreId($storeId);
				$values = array();
				foreach ($product->getMediaAttributes() as $attribute) {
					/* @var $attribute Mage_Eav_Model_Entity_Attribute */
					$values[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
				}
				$result['images_values'] = Zend_Json::encode($values);

				$images = $product->getMediaGallery('images');
				if(is_array($images)) {
					foreach ($images as &$image) {
						$image['url'] = Mage::getSingleton('catalog/product_media_config')->getMediaUrl($image['file']);
						$filePathParts = explode('/', $image['url']);
						$image['file_name'] = end($filePathParts);
					}
				} else {
					$images = array();
				}
				$result['images'] = Zend_Json::encode($images);
			} else {
				$result['error'] = 'Product not fount';
			}
		} catch (Exception $e) {
			$result['error_debug'] = $e->getMessage();
			$result['error'] = 'Images have not been loaded!';
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
	}

	public function saveGalleryConfigAction() {
		$result = array();
		try {
			$storeId = $this->getRequest()->getParam('store', 0);
			$productId = $this->getRequest()->getParam('product_id');
			/** @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel('catalog/product')->load($productId);
			if ($product->getId()) {
				$product->setStoreId($storeId);
				$wasLockedMedia = false;
				if ($product->isLockedAttribute('media')) {
					$product->unlockAttribute('media');
					$wasLockedMedia = true;
				}

				$data = array(
					'media_gallery' => array(
						'images' => $this->getRequest()->getPost('images'),
						'values' => Zend_Json::decode($this->getRequest()->getPost('images_values')),
					),
				);

				$product->addData($data);
				if ($wasLockedMedia) {
					$product->lockAttribute('media');
				}
				$imagesValues = Zend_Json::decode($this->getRequest()->getPost('images_values'));
				foreach ($imagesValues as $code => $value) {
					$product->setData($code, $value != 'no_selection' ? $value : null);
					$product->addAttributeUpdate($code, $value != 'no_selection' ? $value : null, $storeId);
				}
				$product->save();

				$this->loadLayout();
				$this->_renderAjaxGrid();
				return;
			} else {
				$result['error'] = 'Product not fount';
			}
		} catch (Exception $e) {
			$result['error_debug'] = $e->getMessage();
			$result['error'] = 'Images have not been loaded!';
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
	}

	protected function _renderAjaxGrid() {
		if (!$this->getLayout()->getBlock('admin.product.grid')) {
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('adminhtml/catalog_product_grid')->toHtml()
			);
		} else {
			$this->renderLayout();
		}
	}
}
?>