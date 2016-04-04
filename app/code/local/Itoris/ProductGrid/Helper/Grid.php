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

class Itoris_ProductGrid_Helper_Grid extends Itoris_ProductGrid_Helper_Data {

	const INLINE_EDIT = 1;
	const MASS_INLINE_EDIT = 2;

	static public $notAttributes = array('website_ids', 'qty', 'price', 'category_ids', 'sku', 'websites', 'categories', 'entity_id', 'id', 'is_in_stock');

	/** @var null|Itoris_ProductGrid_Model_GridConfig */
	protected $config = null;
	protected $allowedBulkAttributesType = array(
		'textarea',
		'select',
		'multiselect',
		'text',
		'date',
		'price',
        'boolean',
	);
	protected $notAllowedBulkAttributesCode = array(
		'group_price',
		'tier_price',
	);
	protected $notGlobalScopeAttributes = array();

	public function __construct() {
		$this->config = Mage::getModel('itoris_productgrid/gridConfig');
		$this->config->load(Mage::app()->getRequest()->getParam('store', 0));
	}

	public function getGeneralOptions() {
		$options = array(
			array(
				'code'    => 'show_scope',
				'label'   => $this->__('Show Configuration Scope'),
			),
			array(
				'code'    => 'enable_filter',
				'label'   => $this->__('Enable Attribute Filter'),
			),
			array(
				'code'    => 'allow_actions',
				'label'   => $this->__('Allow Actions'),
				'dependent' => 'allow_bulk_apply',
			),
			array(
				'code'    => 'allow_bulk_apply',
				'label'   => $this->__('Allow Bulk Option Apply'),
				'parent'  => 'allow_actions',
			),
			array(
				'code'    => 'allow_reorder',
				'label'   => $this->__('Allow Reorder Grid Columns'),
			),
			array(
				'code'  => 'preview_link',
				'label' => $this->__('Show Preview'),
			),
			array(
				'code'  => 'show_action_column',
				'label' => $this->__('Show Action column'),
			),
			array(
				'code'    => 'allow_inline_edit',
				'label'   => $this->__('Editing Method'),
				'values'  => array(
					array(
						'label' => $this->__('Editing Disabled'),
						'value' => 0,
					),
					array(
						'label' => $this->__('Inline Editing'),
						'value' => self::INLINE_EDIT,
					),
					array(
						'label' => $this->__('After Clicking &quot;Apply Changes&quot;'),
						'value' => self::MASS_INLINE_EDIT,
					)
				),
			),
		);

		return $this->setOptionValues($options, 'code');
	}

	protected function getDefaultSortingValues() {
		$attributes = $this->getAttributesColumns();
		$result = array();
		$catalogHelper = Mage::helper('catalog');
		$headers = array(
			'entity_id' => $catalogHelper->__('ID'),
			'name'      => $catalogHelper->__('Name'),
			'type_id'   => $catalogHelper->__('Type'),
			'attribute_set_id' => $catalogHelper->__('Attrib. Set Name'),
			'sku'        => $catalogHelper->__('SKU'),
			'price'      => $catalogHelper->__('Price'),
			'qty'        => $catalogHelper->__('Qty'),
			'visibility' => $catalogHelper->__('Visibility'),
			'status'     => $catalogHelper->__('Status'),
			'is_in_stock' => $catalogHelper->__('Stock Availability'),
			'created_at'  => $this->__('Creation Date'),
			'updated_at'  => $this->__('Modification Date'),
		);
		$notSortable = array('websites', 'categories');
		foreach ($attributes as $code => $attribute) {
			if ((!isset($attribute['sortable']) || $attribute['sortable']) && !in_array($code, $notSortable)) {
				$result[] = array(
					'label' => isset($headers[$code]) ? $headers[$code] : $attribute['header'],
					'value' => $code,
				);
			}
		}
		return $result;
	}

	protected function setOptionValues($options, $byField) {
		foreach ($options as &$option) {
			if (isset($option['values'])) {
				$asCheckbox = $option['code'] == 'allow_inline_edit' ? true : false;
				$option['value'] = $this->config->getOption($option[$byField], $asCheckbox);
			} else {
				$option['checked'] = (int)$this->config->getOption($option[$byField]);
				if (isset($option['parent'])) {
					$option['disabled'] = !(int)$this->config->getOption($option['parent']);
				}
			}
		}

		return $options;
	}

	/**
	 * Prepare all product attributes without some exceptions (gallery) for config window
	 *
	 * @return array
	 */
	public function getProductAttributes() {
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
			->setOrder('frontend_label', 'ASC')
			->addVisibleFilter();

		$result = array(
			array(
				'attribute_code' => 'entity_id',
				'attribute_id'   => 'entity_id',
				'label'          => $this->__('ID'),
			),
			array(
				'attribute_code' => 'categories',
				'attribute_id'   => 'categories',
				'label'          => $this->__('Categories'),
			),
			array(
				'attribute_code' => 'type_id',
				'attribute_id'   => 'type_id',
				'label'          => $this->__('Product Type'),
			),
			array(
				'attribute_code' => 'attribute_set_id',
				'attribute_id'   => 'attribute_set_id',
				'label'          => $this->__('Attribute Set Name'),
			),
			array(
				'attribute_code' => 'qty',
				'attribute_id'   => 'qty',
				'label'          => $this->__('Qty'),
			),
			array(
				'attribute_code' => 'is_in_stock',
				'attribute_id'   => 'is_in_stock',
				'label'          => $this->__('Stock Availability'),
			),
			array(
				'attribute_code' => 'websites',
				'attribute_id'   => 'websites',
				'label'          => $this->__('Website'),
			),
			array(
				'attribute_code' => 'created_at',
				'attribute_id'   => 'created_at',
				'label'          => $this->__('Creation Date'),'Modification Date '
			),
			array(
				'attribute_code' => 'updated_at',
				'attribute_id'   => 'updated_at',
				'label'          => $this->__('Modification Date'),
				'last_in_group'  => true,
			),
		);

		if ($attributes->getSize()) {
			$notSorted = array();
			foreach ($attributes as $attribute) {
				if ($attribute->getFrontendInput() != 'gallery' && $attribute->getAttributeCode() != 'group_price') {
					$notSorted[] = array(
						'attribute_code' => $attribute->getAttributeCode(),
						'attribute_id'   => $attribute->getAttributeId(),
						'label'          => $attribute->getFrontendLabel(),
					);
				}
			}
			$sorted = $this->sortTopToBottomLeftToRight($notSorted);
			$result = array_merge($result, $sorted);
		}

		return $this->setAttributeValues($result, 'attribute_code');
	}

	protected function sortTopToBottomLeftToRight($data) {
		$qty = count($data);
		$maxInColumn = (int) ($qty / 4) + ($qty % 4 ? 1 : 0);

		$result = array();
		for ($i = 0; $i < $maxInColumn; $i++) {
			for ($j = 0; $j < 4; $j++) {
				$key = $i + $maxInColumn * $j;
				$result[] = isset($data[$key]) ? $data[$key] : array();
			}
		}
		return $result;
	}

	public function getProductAttributesConfig($withAdditional = false) {
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
			->setOrder('frontend_label', 'ASC')
			->addVisibleFilter();
		$result = array();

		if ($attributes->getSize()) {
			/** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
			foreach ($attributes as $attribute) {
				if (in_array($attribute->getFrontendInput(), $this->allowedBulkAttributesType) && !in_array($attribute->getAttributeCode(), $this->notAllowedBulkAttributesCode)) {
					try {
						$attributeData = array(
							'value'   => $attribute->getAttributeId(),
							'label'   => $attribute->getFrontendLabel(),
							'type'    => $attribute->getFrontendInput(),
							'required' => (int)$attribute->getIsRequired(),
						);
						if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
							$attributeData['options'] = $attribute->getSource()->getAllOptions();
						}
						$result[] = $attributeData;
					} catch (Exception $e) {/* some custom attributes haven't source model */}
				}
			}
		}

		return $result;
	}

	protected function setAttributeValues($attributes, $byField) {
		foreach ($attributes as &$attribute) {
			if (!empty($attribute)) {
				$attribute['checked'] = (int)$this->config->getAttribute($attribute[$byField]);
			}
		}

		return $attributes;
	}

	public function getAttributesColumns() {
		$storeId = $this->getStoreId();
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
			->setOrder('frontend_label', 'ASC')
			->addVisibleFilter();
		if ($storeId) {
			$attributes->addStoreLabel($storeId);
		}
		$result = $this->config->getAllAttributesWithOrder();
		$optionsType = array('select', 'multiselect', 'boolean');

		foreach ($attributes as $attribute) {
			if (isset($result[$attribute->getAttributeCode()])) {
				try {
					$result[$attribute->getAttributeCode()]['id'] = $attribute->getId();
					$result[$attribute->getAttributeCode()]['header'] = $attribute->getStoreLabel() ? $attribute->getStoreLabel() : $attribute->getFrontendLabel();
					$result[$attribute->getAttributeCode()]['column_id'] = $attribute->getAttributeCode();
					$result[$attribute->getAttributeCode()]['is_unique'] = $attribute->getIsUnique();
					$result[$attribute->getAttributeCode()]['apply_to'] = $attribute->getApplyTo();
					$result[$attribute->getAttributeCode()]['index'] = $attribute->getAttributeCode();
					$result[$attribute->getAttributeCode()]['type'] = in_array($attribute->getFrontendInput(), $optionsType) ? 'options' : $attribute->getFrontendInput();
					$result[$attribute->getAttributeCode()]['required'] = $attribute->getIsRequired();
					if (in_array($attribute->getFrontendInput(), $optionsType)) {
						if ($storeId) {
							$attribute->setStoreId($storeId);
						}
						$optionsRaw = $attribute->getSource()->getAllOptions();
						$options = array();
						foreach ($optionsRaw as $value) {
							$options[$value['value']] = $value['label'];
						}
						$result[$attribute->getAttributeCode()]['options'] = $options;
						if ($attribute->getFrontendInput() == 'multiselect') {
							$result[$attribute->getAttributeCode()]['multiple'] = true;
							$result[$attribute->getAttributeCode()]['renderer'] = 'itoris_productgrid/admin_catalog_product_grid_column_renderer_multiple';
						}
					}
					if (strpos($attribute->getFrontendInput(), 'image') !== false) {
						$result[$attribute->getAttributeCode()]['filter'] = false;
						$result[$attribute->getAttributeCode()]['sortable'] = false;
						$result[$attribute->getAttributeCode()]['renderer'] = 'itoris_productgrid/admin_catalog_product_grid_column_renderer_image';
					}
					if (!is_null($result[$attribute->getAttributeCode()]['order'])) {
						$orders[$result[$attribute->getAttributeCode()]['order']] = $attribute->getAttributeCode();
					}
				} catch (Exception $e) {}
			}
		}
		$orders = array();
		foreach ($result as $key => $value) {
			if (!is_null($value['order'])) {
				$orders[$value['order']] = $key;
			}
		}
		ksort($orders);
		$sorted = array();
		foreach ($orders as $order) {
			$sorted[$order] = $result[$order];
		}
		foreach ($result as $key => $value) {
			if (!isset($sorted[$key])) {
				$sorted[$key] = $value;
			}
		}

		return $sorted;
	}

	public function getConfigValue($code, $isCheckbox = true) {
		return $this->config->getOption($code, $isCheckbox);
	}

	public function getDefaultConfigJson() {
		$config = $this->config->getDefaultConfig();
		$result = array();
		if (isset($config['options'])) {
			foreach ($config['options'] as $code => $value) {
				$result[] = array(
					'id'    => 'iopt_' . $code,
					'value' => $value,
				);
			}
		}
		if (isset($config['attributes'])) {
			foreach ($config['attributes'] as $code => $value) {
				$result[] = array(
					'id'    => 'opt_' . $code,
					'value' => $value,
				);
			}
		}

		return Zend_Json::encode($result);
	}

	public function getAttributesCodeForSet($setId) {
		/** @var $attributes Mage_Eav_Model_Mysql4_Entity_Attribute_Collection */
		$attributes = Mage::getModel('eav/mysql4_entity_attribute_collection');
		$attributes->setAttributeSetFilter($setId)->load();
		$codes = array();
		foreach ($attributes as $attribute) {
			$codes[] = $attribute->getAttributeCode();
		}
		return $codes;
	}

	public function getActionColumnOrder($storeId = 0) {
		return $this->config->getColumnOrder('action_column_order');
	}

	public function getMassactionColumnOrder($storeId = 0) {
		return $this->config->getColumnOrder('massaction_column_order');
	}

	public function getColumnOrders() {
		return $this->config->getColumnOrders();
	}

	public function addScopeAttribute($attributeCode) {
		$this->notGlobalScopeAttributes[] = $attributeCode;
		return $this;
	}

	public function isScopeAttribute($attributeCode) {
		return in_array($attributeCode, $this->notGlobalScopeAttributes);
	}
}
?>