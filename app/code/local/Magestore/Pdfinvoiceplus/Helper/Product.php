<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Pdfinvoiceplus Helper
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Helper_Product extends Mage_Catalog_Helper_Product
{

    /**
     * Get the non system attributes for the variables
     * @return array
     */
    public function getAllNonSystemAttributes()
    {
        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('is_user_defined', '1');

        $attrs = array();

        foreach ($productAttrs as $attribute)
        {
            $attrs[$attribute->getData('attribute_code')] = array(
                'label' => $attribute->getData('frontend_label'),
            );
        }
        return $attrs;
    }

    /**
     * 
     * @return type
     */
    public function loadTheProduct()
    {
        return Mage::getModel('catalog/product');
    }

    /**
     * 
     * @param integer $productId
     * @return boolean
     */
    public function isConfigurable($productId)
    {
        if ($this->loadTheProduct()->load($productId)->getData('type_id') == 'configurable')
        {
            return true;
        }
    }

    /**
     * Get the product id ti get the user added attributes
     * @param integer $productId
     * @return array
     * 
     * Need to add store selection for the labels ! IMPORTANT
     */
    public function getDataAsVar($productId, $storeId, $child = false)
    {
        $product = $this->loadTheProduct()->load($productId);
        $data = array();

        $gettingTheVariablesFromArrayKey = array_keys($this->getAllNonSystemAttributes());
        $gettingTheLabelsFromArrayKey = $this->getAllNonSystemAttributes();

        foreach ($gettingTheVariablesFromArrayKey as $variables)
        {
            /* Edit By Jack 02/12 */
            if ($product->offsetExists($variables)&& $product->getAttributeText($variables))
            {
                $data[$variables] = array(
                    'value' => $product->getAttributeText($variables),
                    'label' => $gettingTheLabelsFromArrayKey[$variables]['label']
                );
            } else
            {
                if ($product->getData($variables))
                {
                    $data[$variables] = array(
                        'value' => $product->getData($variables),
                        'label' => $gettingTheLabelsFromArrayKey[$variables]['label']
                    );
                }
            }
			
            /* End Edit */

            $data['weight'] = array(
                'value' => $product->getData('weight'),
                'label' => Mage::helper('pdfinvoiceplus')->__('Product weight')
            );
            $data['description'] = array(
                'value' => $product->getData('description'),
                'label' => Mage::helper('pdfinvoiceplus')->__('Product description')
            );
            $data['short_description'] = array(
                'value' => $product->getData('short_description'),
                'label' => Mage::helper('pdfinvoiceplus')->__('Product short description')
            );
            if (!$child)
            {
                $data['url_path'] = array(
                    'value' => Mage::app()->getStore($storeId)
                        ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) 
                        . $product->getData('url_path'),
                    'label' => Mage::helper('pdfinvoiceplus')->__('Product url path')
                );
            }
        }
        return $data;
    }

    /**
     * Get the product image - need to add the user options here
     * @param type $productId
     * @return array getPlaceholder no_selection
     */
    /* Change By Jack 19/01/2015 */
    public function convertImage($url){
        $type = pathinfo($url, PATHINFO_EXTENSION);
        $data = file_get_contents($url);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
    public function getTheProductImage($productId)
    {
        $_product = $this->loadTheProduct()->load($productId);
        $imageFile = $_product->getData('small_image');
        if ($imageFile !== 'no_selection' && isset($imageFile))
        {
            $_image = Mage::helper('catalog/image')->init($_product, 'small_image', $imageFile)->resize(77, 77);
            $imageFile = $_product->getData('small_image');
            $base64Url = $this->convertImage($_image->__toString());
            $image = array(
                'items_small_image' => array(
                    'value' => '<img src="' . $base64Url . '" />',
                    'label' => Mage::helper('pdfinvoiceplus')->__('Product image')
                ),
            );
        } else
        {
            $image = array(
                'items_small_image' => array(
                    'value' => '',
                    'label' => ''
                ),
            );
        }
        return $image;
    }
    /* End Change */
}

