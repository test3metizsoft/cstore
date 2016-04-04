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
 * Statetax admin edit form
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Statetax_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_controller = 'adminhtml_statetax';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('metizsoft_taxgenerate')->__('Save Statetax')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('metizsoft_taxgenerate')->__('Delete Statetax')
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
        if (Mage::registry('current_statetax') && Mage::registry('current_statetax')->getId()) {
            return Mage::helper('metizsoft_taxgenerate')->__(
                "Edit Statetax '%s'",
                $this->escapeHtml(Mage::registry('current_statetax')->getState())
            );
        } else {
            return Mage::helper('metizsoft_taxgenerate')->__('Add Statetax');
        }
    }
}
