<?php
/**
 * Metizsoft_Taxgenerate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Metizsoft
 * @package        Metizsoft_Taxgenerate
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Citytax admin edit tabs
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Citytax_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Metizsoft Solutions
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('citytax_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('metizsoft_taxgenerate')->__('Citytax'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Citytax_Edit_Tabs
     * @author Metizsoft Solutions
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_citytax',
            array(
                'label'   => Mage::helper('metizsoft_taxgenerate')->__('Citytax'),
                'title'   => Mage::helper('metizsoft_taxgenerate')->__('Citytax'),
                'content' => $this->getLayout()->createBlock(
                    'metizsoft_taxgenerate/adminhtml_citytax_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_citytax',
                array(
                    'label'   => Mage::helper('metizsoft_taxgenerate')->__('Store views'),
                    'title'   => Mage::helper('metizsoft_taxgenerate')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'metizsoft_taxgenerate/adminhtml_citytax_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve citytax entity
     *
     * @access public
     * @return Metizsoft_Taxgenerate_Model_Citytax
     * @author Metizsoft Solutions
     */
    public function getCitytax()
    {
        return Mage::registry('current_citytax');
    }
}
