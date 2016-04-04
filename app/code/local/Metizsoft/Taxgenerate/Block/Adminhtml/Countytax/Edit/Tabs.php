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
 * Countytax admin edit tabs
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('countytax_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('metizsoft_taxgenerate')->__('Countytax'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Edit_Tabs
     * @author Metizsoft Solutions
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_countytax',
            array(
                'label'   => Mage::helper('metizsoft_taxgenerate')->__('Countytax'),
                'title'   => Mage::helper('metizsoft_taxgenerate')->__('Countytax'),
                'content' => $this->getLayout()->createBlock(
                    'metizsoft_taxgenerate/adminhtml_countytax_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_countytax',
                array(
                    'label'   => Mage::helper('metizsoft_taxgenerate')->__('Store views'),
                    'title'   => Mage::helper('metizsoft_taxgenerate')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'metizsoft_taxgenerate/adminhtml_countytax_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve countytax entity
     *
     * @access public
     * @return Metizsoft_Taxgenerate_Model_Countytax
     * @author Metizsoft Solutions
     */
    public function getCountytax()
    {
        return Mage::registry('current_countytax');
    }
}
