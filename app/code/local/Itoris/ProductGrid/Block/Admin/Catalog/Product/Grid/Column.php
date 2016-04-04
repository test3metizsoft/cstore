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

class Itoris_ProductGrid_Block_Admin_Catalog_Product_Grid_Column extends Mage_Adminhtml_Block_Widget_Grid_Column {

	static protected $attributeSetCodes = array();
	
	protected $allAttributes;
	
	/**
	 * Get row field html with inline edit attribute html
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function getRowField(Varien_Object $row) {
		$renderedValue = parent::getRowField($row);
		if (!$this->allAttributes) $this->allAttributes = Mage::helper('itoris_productgrid/grid')->getAttributesColumns();

		if ($this->getIsInlineEditable() && $this->getGridHelper()->getConfigValue('allow_inline_edit')) {
			$rowValue = $this->getRowValue($row);
			if ($this->getMultiple() && !is_array($rowValue)) {
				$rowValue = explode(',', $rowValue);
			}
			$validation = $this->getRequired() ? 'required-entry' : '';
			$renderedValue = '<div class="itoris-ceil-loading" style="display:none;"></div><div class="itoris-inline-edit-value">' . $renderedValue . '</div>';
			$renderedValue .= '<div class="itoris-inline-edit-ceil" style="display:none;"><div class="itoris-icons"><div class="itoris-cancel-icon"></div><div class="itoris-save-icon"></div></div>';
			if ($this->getType() == 'options') {
				$validation = $this->getRequired() ? 'validate-select' : '';
				$renderedValue .= '<select class="itoris-inline-edit-ceil-value '.$validation.'" name="' . $this->getIndex() . '" '. ($this->getMultiple() ? 'multiple="multiple"' : '') .' style="width:98%;">';
				foreach ($this->getOptions() as $value => $option) {
					$renderedValue .= '<option value="' . $this->htmlSpecialChars($value) . '" ' . ($value == $rowValue || (is_array($rowValue) && in_array($value, $rowValue)) ? 'selected="selected"' : '') . '>' . $this->htmlSpecialChars($option) . '</option>';
				}
				$renderedValue .= '</select>';
			} elseif ($this->getType() == 'textarea') {
				$renderedValue .= '<textarea class="itoris-inline-edit-ceil-value '.$validation.'" name="' . $this->getIndex() . '" style="width:98%;">' . $this->htmlSpecialChars($rowValue) . '</textarea>';
			} elseif ($this->getType() == 'date') {
				$elmId = 'attr_date_' . $this->getIndex() . $row->getId();
				$dateFormat = 'M/d/yyyy';
				$displayFormat = Varien_Date::convertZendToStrFtime($dateFormat, true, false);
				$value = $rowValue ? new Zend_Date($rowValue, Varien_Date::DATETIME_INTERNAL_FORMAT) : null;
				$renderedValue .= '<input class="itoris-inline-edit-ceil-value ceil-date '.$validation.'" name="' . $this->getIndex() . '" value="' . ($value ? $this->htmlSpecialChars($value->toString($dateFormat)) : '') . '" id="' . $elmId . '" style="width:110px !important;" />'
						.' <img src="' . $this->getSkinUrl('images/grid-cal.gif') . '" alt="" class="v-middle" id="' . $elmId . '_trig" title="%s" /></div>'
						. '<script type="text/javascript">
							//<![CDATA[
							Calendar.setup({
								inputField: "'.$elmId.'",
								ifFormat: "'.$displayFormat.'",
								showsTime: false,
								button: "'.$elmId.'_trig",
								align: "Bl",
								singleClick : true
							});
							//]]>
							</script>';
			} elseif ($this->getType() == 'media_image') {
				$renderedValue .= '<div class="itoris-inline-edit-ceil-value itoris-media-image"></div>';
			} else {
				if ($this->getType() == 'price') {
					$rowValue = sprintf('%.2f', $rowValue);
				} else if ($this->getIndex() == 'qty') {
					$rowValue = (float)$rowValue;
				}
				$renderedValue .= '<input type="text" class="itoris-inline-edit-ceil-value '.$validation.'" value="' . $this->htmlSpecialChars($rowValue) . '" name="' . $this->getIndex() . '" style="width:98%;"/>';
			}
			$renderedValue .= '<span class="itoris-product-id" style="display:none;">' . $row->getId() . '</span>';
			$codes = $this->getAttributesCodeForSet($row->getAttributeSetId());
			$notEditable = ($this->getIndex() == 'qty' && in_array($row->getTypeId(), array('grouped', 'configurable', 'bundle')))
				|| ($this->getIndex() == 'price' && in_array($row->getTypeId(), array('grouped', 'bundle')))
				|| isset($this->allAttributes[$this->getIndex()]) && count((array) @$this->allAttributes[$this->getIndex()]['apply_to']) > 0 && !in_array($row->getTypeId(), (array) @$this->allAttributes[$this->getIndex()]['apply_to']);
			if ($this->getIndex() == 'custom_name') {
				$codes[] = 'custom_name';
			}
			if ($notEditable || (!in_array($this->getIndex(), $codes) && !in_array($this->getIndex(), Itoris_ProductGrid_Helper_Grid::$notAttributes))) {
				$renderedValue .= '<span class="not-editable" style="display:none;"></span>';
			} else {
				if ($this->getGridHelper()->isScopeAttribute($this->getIndex())) {
					if ((int)$row->getData($this->getIndex() . '_scope_id')) {
						$renderedValue .= '<a href="#" class="use-default-scope-link">' . $this->__('Use Default Scope') . '</a>';
					}
				}
			}
			$renderedValue .= '</div>';
		}
		return $renderedValue;
	}

	protected function getRowValue($row) {
		if ($getter = $this->getGetter()) {
			if (is_string($getter)) {
				return $row->$getter();
			} elseif (is_callable($getter)) {
				return call_user_func($getter, $row);
			}
			return '';
		}
		return $row->getData($this->getIndex());
	}

	protected function htmlSpecialChars($str) {
		$str = htmlspecialchars($str, ENT_COMPAT);
		return preg_replace('/(&amp;nbsp;)/', '&nbsp;', $str);
	}

	protected function getAttributesCodeForSet($setId) {
		if (!isset(self::$attributeSetCodes[$setId])) {
			self::$attributeSetCodes[$setId] = Mage::helper('itoris_productgrid/grid')->getAttributesCodeForSet($setId);
		}
		return self::$attributeSetCodes[$setId];
	}

	/**
	 * @return Itoris_ProductGrid_Helper_Grid
	 */
	protected function getGridHelper() {
		return Mage::helper('itoris_productgrid/grid');
	}
}
?>