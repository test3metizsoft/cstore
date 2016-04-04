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
 * Pdfinvoiceplus Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pdfinvoiceplus_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pdfinvoiceplus')->__('Template Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $companyinfo = $this
            ->getLayout()
            ->createblock('pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_companyinfo')
            ->toHtml();
        $insertvariable = $this->getLayout()->createBlock('pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_insertvariable')->tohtml();
        $this->addTab('information_section', array(
            'label'     => Mage::helper('pdfinvoiceplus')->__('General Information'),
            'title'     => Mage::helper('pdfinvoiceplus')->__('General Information'),
            'content'   => $this->getLayout()
                                ->createBlock('pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_information')
//                                ->toHtml().$insertvariable,
                                ->toHtml().$companyinfo.$insertvariable,
        ));
        
//        $html =  $this->getLayout()
//                                ->createBlock('pdfinvoiceplus/adminhtml_design_tab')
//                                ->setTemplate('pdfinvoiceplus/design.phtml')
//                                ->toHtml();
//        $this->addTab('design_template_section', array(
//            'label'     => Mage::helper('pdfinvoiceplus')->__('Design'),
//            'title'     => Mage::helper('pdfinvoiceplus')->__('Design'),
//            'content'   => $html
//        ));
//        if($this->getRequest()->getParam('tab') == 'design'){
//            $this->setActiveTab('design_template_section');
//        }
        return parent::_beforeToHtml();
    }
}