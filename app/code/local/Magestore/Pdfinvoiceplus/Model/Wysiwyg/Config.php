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
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Model_Wysiwyg_Config extends Mage_Cms_Model_Wysiwyg_Config
{

    /**
     * 
     * @param Varien_Object
     * @return Varien_Object
     */
    public function getConfig($data = array())
    {
        $config = parent::getConfig($data);

        $newOptiones = Mage::getSingleton('pdfinvoiceplus/variables_optiones')->getWysiwygPluginSettings($config);
        
        if (isset($newOptiones['plugins'][1]) && is_array($newOptiones['plugins'][1]))
        {
            $config->setData('plugins', array($newOptiones['plugins'][1]));
        }

        $config->setData('files_browser_window_url', Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index/'));
        $config->setData('directives_url', Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'));
        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));
        $config->setData('widget_window_url', Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'));
        $config->setData('add_variables', true);

        return $config;
    }

}
