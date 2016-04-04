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
 * Pdfinvoiceplus Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edithtml extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData()) {
            $data = Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData();
            Mage::getSingleton('adminhtml/session')->setPdfinvoiceplusData(null);
        } elseif (Mage::registry('pdfinvoiceplus_data')) {
            $data = Mage::registry('pdfinvoiceplus_data')->getData();
        }
        
        
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $wysiwygConfig->addData(array(
            'add_variables'		=> false,
            'plugins'			=> array(),
            'widget_window_url'	=> Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
            'directives_url'	=> Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
            'directives_url_quoted'	=> preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
            'files_browser_window_url'	=> Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
        ));
        $fieldset = $form->addFieldset('pdfinvoiceplus_information', array(
            'legend' => Mage::helper('pdfinvoiceplus')->__('General Information')
        ));
        $fieldset->addField('header', 'editor', array(
            'label'     =>   Mage::helper('pdfinvoiceplus')->__('Header Content'),
            'title'     =>   Mage::helper('pdfinvoiceplus')->__('Header Content'),
            'name'      =>  'header',
            'wysiwyg'   => true,
            'style'     => 'width:600px;',
            'config'    =>  $wysiwygConfig
            )
        );
    }
    
    
}