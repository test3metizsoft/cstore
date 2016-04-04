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
 * Citytax admin edit form
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Citytax_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'metizsoft_taxgenerate';
        $this->_controller = 'adminhtml_citytax';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('metizsoft_taxgenerate')->__('Save Citytax')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('metizsoft_taxgenerate')->__('Delete Citytax')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('metizsoft_taxgenerate')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Metizsoft Solutions
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_citytax') && Mage::registry('current_citytax')->getId()) {
            $nameofthecounty =Mage::getSingleton('metizsoft_taxgenerate/countylist')->load(Mage::registry('current_citytax')->getCountyId());
            $nameofthecity =Mage::getSingleton('metizsoft_taxgenerate/citylist')->load(Mage::registry('current_citytax')->getCityId());
            $nameofthecategory =Mage::getSingleton('catalog/category')->load(Mage::registry('current_citytax')->getCategoryId());
            return Mage::helper('metizsoft_taxgenerate')->__(
                "Edit Citytax '%s'",
                $this->escapeHtml(Mage::registry('current_citytax')->getState())
                    .' / '.$this->escapeHtml($nameofthecounty->getName())
                    .' / '.$this->escapeHtml($nameofthecity->getCity())
                    .' / '.$this->escapeHtml($nameofthecategory->getName())
            );
        } else {
            return Mage::helper('metizsoft_taxgenerate')->__('Add Citytax');
        }
    }
}
