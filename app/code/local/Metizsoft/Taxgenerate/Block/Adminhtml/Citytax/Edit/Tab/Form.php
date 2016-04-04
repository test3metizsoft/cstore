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
 * Citytax edit form tab
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Citytax_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Block_Adminhtml_Citytax_Edit_Tab_Form
     * @author Metizsoft Solutions
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('citytax_');
        $form->setFieldNameSuffix('citytax');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'cityl_form',
            array('legend' => Mage::helper('metizsoft_taxgenerate')->__('Citytax'))
        );
        $state = $fieldset->addField(
            'state',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('State Name'),
                'name'  => 'state',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> Mage::getModel('metizsoft_taxgenerate/statetax_attribute_source_state')->getAllOptions(true),
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
                        $('citytax_county_id').update('Searching...');
                    },
                    onComplete: function(stateform) {
                        $('citytax_county_id').update(stateform.responseText);
                        $('citytax_city_id').update('<option>Please Select</option>');
                    }
                });
            }
        </script>");
        $county = $fieldset->addField(
            'county_id',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('County Name'),
                'name'  => 'county_id',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> Mage::getModel('metizsoft_taxgenerate/statetax_attribute_source_county')->getAllOptions(true),
                'onchange' => 'getcity(this)',
           )
        );
$county->setAfterElementHtml("<script type=\"text/javascript\">
            function getcity(selectElement){
                var reloadurl = '". Mage::helper("adminhtml")->getUrl('adminhtml/taxgenerate_statetax/city') . "';
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    parameters: {county: selectElement.value},
                    onLoading: function (stateform) {
                        $('citytax_city_id').update('Searching...');
                    },
                    onComplete: function(stateform) {
                        $('citytax_city_id').update(stateform.responseText);
                    }
                });
            }
        </script>");

        $city = $fieldset->addField(
            'city_id',
            'select',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('City name'),
                'name'  => 'city_id',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> Mage::getModel('metizsoft_taxgenerate/statetax_attribute_source_city')->getAllOptions(true),
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
            'city_tax',
            'text',
            array(
                'label' => Mage::helper('metizsoft_taxgenerate')->__('City Tax Amount'),
                'name'  => 'city_tax',
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
            Mage::registry('current_citytax')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_citytax')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getCitytaxData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getCitytaxData());
            Mage::getSingleton('adminhtml/session')->setCitytaxData(null);
        } elseif (Mage::registry('current_citytax')) {
            $formValues = array_merge($formValues, Mage::registry('current_citytax')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
