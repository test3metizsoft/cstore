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
 * Countylist edit form tab
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Countylist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countylist_Edit_Tab_Form
     * @author Metizsoft Solutions
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('countylist_');
        $form->setFieldNameSuffix('countylist');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'countylist_form',
            array('legend' => Mage::helper('metizsoft_taxgenerate')->__('Countylist'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'state',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('State'),
                'name'  => 'state',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('metizsoft_taxgenerate/countylist_attribute_source_state')->getAllOptions(true),
           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('metizsoft_taxgenerate')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('metizsoft_taxgenerate')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('metizsoft_taxgenerate')->__('Disabled'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_countylist')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_countylist')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getCountylistData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getCountylistData());
            Mage::getSingleton('adminhtml/session')->setCountylistData(null);
        } elseif (Mage::registry('current_countylist')) {
            $formValues = array_merge($formValues, Mage::registry('current_countylist')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
