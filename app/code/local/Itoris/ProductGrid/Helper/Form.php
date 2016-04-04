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

class Itoris_ProductGrid_Helper_Form extends Itoris_ProductGrid_Helper_Data {

	private $yesNoValues = array(
		array(
			'value' => 1,
			'label' => 'Yes',
		),
		array(
			'value' => 0,
			'label' => 'No',
		),
	);

	public function getYesNoOptionValues() {
		return $this->prepareValues($this->yesNoValues);
	}

	private function prepareValues($values, $withoutValues = array()) {
		foreach ($values as $key => $value) {
			if (!in_array($value['value'], $withoutValues)) {
				$values[$key]['label'] = $this->__($value['label']);
			} else {
				unset($values[$key]);
			}
		}

		return $values;
	}

	/**
	 * Prepare elements values for form
	 *
	 * @param $form Varien_Data_Form
	 *
	 * @return array
	 */
	public function prepareElementsValues($form) {
		$values = array();
		$fieldsets = $form->getElements();
		$checkStore = (bool)Mage::app()->getRequest()->getParam('store');
		foreach ($fieldsets as $fieldset) {
			if (get_class($fieldset) == 'Varien_Data_Form_Element_Fieldset') {
				foreach ($fieldset->getElements() as $element) {
					if ($id = $element->getId()) {
						$value = $this->getSettings(true)->getSettingsValue($id);
						if ($value !== null) {
							$values[$id] = $value;
						}
						if ($element->getType() == 'checkbox' && $value) {
							$element->setIsChecked($value);
						}
						$element->setUseParent($this->getSettings(true)->isParentValue($id));
						$element->setUseScope($checkStore ? $this->__('Use Default') : null);
					}
				}
			}
		}

		return $values;
	}

	public function getUseScope() {
		$checkStore = (bool)Mage::app()->getRequest()->getParam('store');
		return $checkStore ? $this->__('Use Default') : null;
	}

	/**
	 * This function should be deleted
	 *
	 * @return null
	 */
	public function getUseScopeWithoutWebsite() {
		return $this->getUseScope();
	}

	public function getIsParentValue($settingId) {
		return $this->getSettings(true)->isParentValue($settingId);
	}

	/**
	 * Is setting value default
	 *
	 * @return bool
	 */
	protected function isDefaultSettings() {
		return !Mage::app()->getRequest()->getParam('store');
	}

}
?>