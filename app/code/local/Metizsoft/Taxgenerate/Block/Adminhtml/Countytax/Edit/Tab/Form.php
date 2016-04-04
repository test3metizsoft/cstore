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
 * Countytax edit form tab
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Countytax_Edit_Tab_Form
     * @author Metizsoft Solutions
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('countytax_');
        $form->setFieldNameSuffix('countytax');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'countytax_form',
            array('legend' => Mage::helper('metizsoft_taxgenerate')->__('Countytax'))
        );

        $state = $fieldset->addField(
            'state',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('State Name'),
                'name'  => 'state',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> Mage::getModel('metizsoft_taxgenerate/countytax_attribute_source_state')->getAllOptions(true),
                'onchange' => 'getcounty(this)',
           )
        );
        
        $state->setAfterElementHtml("<script type=\"text/javascript\">
            function getcounty(selectElement){
            
                var reloadurl = '". Mage::helper("adminhtml")->getUrl('adminhtml/taxgenerate_statetax/county') . "';
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    parameters: {state: selectElement.value},
                    onLoading: function (stateform) {
                        $('countytax_county_id').update('Searching...');
                    },
                    onComplete: function(stateform) {
                        $('countytax_county_id').update(stateform.responseText);
                    }
                });
            }
        </script>");
        
        $fieldset->addField(
            'county_id',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('County Name'),
                'name'  => 'county_id',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> Mage::getModel('metizsoft_taxgenerate/citylist_attribute_source_county')->getAllOptions(true),
           )
        );
        
        $fieldset->addField(
            'category_id',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('Category'),
                'name'  => 'category_id',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> Mage::getModel('metizsoft_taxgenerate/statetax_attribute_source_category')->getAllOptions(true),
           )
        );
        
        $state = $fieldset->addField(
            'taxtype',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('Tax type'),
                'name'  => 'taxtype',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> array(''=>'Please Select..','fix' => 'Fix Amount','per' => 'Percentage'),
            )
        );
        
        $fieldset->addField(
            'county_tax',
            'text',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('County Tax Amount'),
                'name'  => 'county_tax',
                'required'  => true,
                'class' => 'required-entry',
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
            Mage::registry('current_countytax')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_countytax')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getCountytaxData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getCountytaxData());
            Mage::getSingleton('adminhtml/session')->setCountytaxData(null);
        } elseif (Mage::registry('current_countytax')) {
            $formValues = array_merge($formValues, Mage::registry('current_countytax')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
