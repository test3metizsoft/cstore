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
 * @copyright  Copyright (c) 2013 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */


class Itoris_ProductGrid_Block_Admin_Catalog_Product_Grid_Gallery extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content {

	protected $mediaAttributes = null;

	public function __construct() {
		parent::__construct();
		$this->setId('itoris_productgrid_gallery');
		$this->setTemplate('itoris/productgrid/product/gallery.phtml');
	}

	protected function _prepareLayout() {
		$this->setChild('uploader',
			$this->getLayout()->createBlock('adminhtml/media_uploader')
		);

		$this->getUploader()->getConfig()
			->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('adminhtml/catalog_product_gallery/upload'))
			->setFileField('image')
			->setFilters(array(
				'images' => array(
					'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
					'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
				)
			));

		//Mage::dispatchEvent('catalog_product_gallery_prepare_layout', array('block' => $this));

		return parent::_prepareLayout();
	}

	public function getImagesJson() {
		return '[]';
	}

	public function getImagesValuesJson() {
		$values = array();
		foreach ($this->getMediaAttributes() as $attribute) {
			/* @var $attribute Mage_Eav_Model_Entity_Attribute */
			$values[$attribute->getAttributeCode()] = null;
		}
		return Mage::helper('core')->jsonEncode($values);
	}

	public function getImageTypes() {
		$imageTypes = array();
		foreach ($this->getMediaAttributes() as $attribute) {
			/* @var $attribute Mage_Eav_Model_Entity_Attribute */
			$imageTypes[$attribute->getAttributeCode()] = array(
				'label' => $attribute->getFrontend()->getLabel() . ' '
				. Mage::helper('catalog')->__($this->getScopeLabel($attribute)),
				'field' => $attribute->getAttributeCode(),
			);
		}
		return $imageTypes;
	}

	public function getScopeLabel($attribute) {
		return '';
	}

	public function hasUseDefault() {
		foreach ($this->getMediaAttributes() as $attribute) {
			if ($this->canDisplayUseDefault($attribute)) {
				return true;
			}
		}

		return false;
	}

	public function canDisplayUseDefault($attribute) {
		return false;
	}

	public function getMediaAttributes() {
		if (is_null($this->mediaAttributes)) {
			$this->mediaAttributes = array();
			/** @var Mage_Eav_Model_Config $eavConfig */
			$eavConfig = Mage::getSingleton('eav/config');
			$entityType = $eavConfig->getEntityType('catalog_product');
			$attributesCodes = array('thumbnail', 'small_image', 'image');
			foreach ($attributesCodes as $attributeCode) {
				/* @var $attribute Mage_Eav_Model_Entity_Attribute */
				$attribute = Mage::getModel('eav/entity_attribute');
				$attribute->loadByCode($entityType, $attributeCode);
				if ($attribute->getId()) {
					$this->mediaAttributes[] = $attribute;
				}
			}
		}
		return $this->mediaAttributes;
	}

}
?>
