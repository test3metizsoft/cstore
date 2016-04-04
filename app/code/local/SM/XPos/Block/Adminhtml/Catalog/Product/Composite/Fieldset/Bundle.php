<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml block for fieldset of bundle product
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SM_XPos_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Bundle
    extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    /**
     * Returns string with json config for bundle product
     *
     * @return string
     */
    public function getJsonConfig() {
        $options = array();
        $optionsArray = $this->getOptions();
        foreach ($optionsArray as $option) {
            $optionId = $option->getId();
            $options[$optionId] = array('id' => $optionId, 'title' => $option->getTitle(),'selections' => array());
            if($defaultSelections = $option->getSelections()) {
                foreach ($defaultSelections as $selection) {
                    $options[$optionId]['selections'][$selection->getSelectionId()] = array(
                        'can_change_qty' => $selection->getSelectionCanChangeQty(),
                        'default_qty'    => $selection->getSelectionQty(),
                        'price'    => $this->getSelectionPrice($selection),
                        'name'    => $selection->getName(),
                    );
                }
            }
        }
        $config = array('options' => $options);
        return Mage::helper('core')->jsonEncode($config);
    }

    public function getSelectionPrice($_selection)
    {
        $price = 0;
        $store = $this->getProduct()->getStore();
        if ($_selection) {
            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
            if (is_numeric($price)) {
                $price = $this->helper('core')->currencyByStore($price, $store, false);
            }
        }
        return is_numeric($price) ? $price : 0;
    }
}
