<?php

class Metizsoft_Reports_Block_Adminhtml_Filter_Formcity extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * This will contain our form element's visibility
     * @var array
     */
    protected $_fieldVisibility = array();

    /**
     * Field options
     * @var array
     */
    protected $_fieldOptions = array();

    /**
     * Sets a form element to be visible or not
     * @param string $fieldId
     * @param bool $visibility
     * @return Metizsoft_Reports_Block_Adminhtml_Filter_Form
     */
    public function setFieldVisibility($fieldId, $visibility) {
        $this->_fieldVisibility[$fieldId] = $visibility ? true : false;
        return $this;
    }

    /**
     * Returns the field is visible or not. If we hadn't set a value
     * for the field previously, it will return the value defined in the
     * defaultVisibility parameter (it's true by default)
     * @param string $fieldId
     * @param bool $defaultVisibility
     * @return bool
     */
    public function getFieldVisibility($fieldId, $defaultVisibility = true) {
        if (isset($this->_fieldVisibility[$fieldId])) {
            return $this->_fieldVisibility[$fieldId];
        }
        return $defaultVisibility;
    }

    /**
     * Set field option(s)
     * @param string $fieldId
     * @param string|array $option if option is an array, loop through it's keys and values
     * @param mixed $value if option is an array this option is meaningless
     * @return Metizsoft_Reports_Block_Adminhtml_Filter_Form
     */
    public function setFieldOption($fieldId, $option, $value = null) {
        if (is_array($option)) {
            $options = $option;
        } else {
            $options = array($option => $value);
        }

        if (!isset($this->_fieldOptions[$fieldId])) {
            $this->_fieldOptions[$fieldId] = array();
        }

        foreach ($options as $key => $value) {
            $this->_fieldOptions[$fieldId][$key] = $value;
        }

        return $this;
    }

    /**
     * Prepare our form elements
     * @return Metizsoft_Reports_Block_Adminhtml_Filter_Form
     */
    protected function _prepareForm() {
        // inicialise our form
        $actionUrl = $this->getCurrentUrl();
        $form = new Varien_Data_Form(array(
            'id' => 'filter_form',
            'action' => $actionUrl,
            'method' => 'get'
        ));

        // set ID prefix for all elements in our form
        $htmlIdPrefix = 'metizsoft_reports_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        // create a fieldset to add elements to
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('metizsoft_reports')->__('Filter')));

        // prepare our filter fields and add each to the fieldset
        // date filter
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        $fieldset->addField('register', 'button', array(
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/resetalldata?report=cityreport') . '\')',
            'value' => 'Reset all new data',
            'style' => 'display:none',
        ));
        $fieldset->addField('from', 'date', array(
            'name' => 'from',
            'format' => $dateFormatIso,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => Mage::helper('metizsoft_reports')->__('Date From'),
            'title' => Mage::helper('metizsoft_reports')->__('Date From')
        ));
        
        $fieldset->addField('to', 'date', array(
            'name' => 'to',
            'format' => $dateFormatIso,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => Mage::helper('metizsoft_reports')->__('Date To'),
            'title' => Mage::helper('metizsoft_reports')->__('Date To')
        ));

        $statelist = Mage::getModel('directory/country')->load('US')->getRegions();
        $newstlist = array('' => 'All');
        $j = 1;
        foreach ($statelist as $state) {
            $j = $j + 1;
            $newstlist[$j]['value'] = $state->getCode();
            $newstlist[$j]['label'] = $state->getName();
        }


        $fieldset->addField('state', 'select', array(
            'name' => 'state',
            'options' => $this->_getCounryOptions(),
            'values' => $newstlist,
            'label' => Mage::helper('metizsoft_reports')->__('State')
        ));

        $countylist = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()->toOptionArray();
        $countylistarray = array('' => 'All');
        $j = 1;
        foreach ($countylist as $county) {
            $j = $j + 1;
            $countylistarray[$j]['value'] = $county['label'];
            $countylistarray[$j]['label'] = $county['label'];
        }
        $fieldset->addField('county', 'select', array(
            'name' => 'county',
            'values' => $countylistarray,
            'label' => Mage::helper('metizsoft_reports')->__('County')
        ));



        $citylist = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()->toOptionArray();
        $citylistarray = array('' => 'All');
        $j = 1;
        foreach ($citylist as $city) {
            $j = $j + 1;
            $citylistarray[$j]['value'] = $city['label'];
            $citylistarray[$j]['label'] = $city['label'];
        }
        $fieldset->addField('city', 'select', array(
            'name' => 'city',
            'options' => $this->_getCounryOptions(),
            'values' => $citylistarray,
            'label' => Mage::helper('metizsoft_reports')->__('City')
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

    /**
     * Get period type options
     * @return array
     */
    protected function _getPeriodTypeOptions() {
        $options = array(
            'day' => Mage::helper('metizsoft_reports')->__('Day'),
            'month' => Mage::helper('metizsoft_reports')->__('Month'),
            'year' => Mage::helper('metizsoft_reports')->__('Year'),
        );

        return $options;
    }

    /** Get the United state option */
    protected function _getCounryOptions() {
        $options = array(
            'united state' => Mage::helper('metizsoft_reports')->__('United State'),
        );

        return $options;
    }

    public function _getRegion() {
        $regionsCollection = Mage::getResourceModel('directory/region_collection')->addCountryFilter('US')->load();
        $regionName = $regionsCollection->getData();
        echo "<pre>";
        print_r(array_column($regionName, 'name'));
        die();
    }

    /**
     * Returns options for shipping rate select
     * @return array
     */
    protected function _getShippingRateSelectOptions() {
        $options = array(
            '0' => 'Any',
            '1' => 'Specified'
        );

        return $options;
    }

    /**
     * Inicialise form values
     * Called after prepareForm, we apply the previously set values from filter on the form
     * @return Metizsoft_Reports_Block_Adminhtml_Filter_Form
     */
    protected function _initFormValues() {
        $filterData = $this->getFilterData();
        $this->getForm()->addValues($filterData->getData());
        return parent::_initFormValues();
    }

    /**
     * Apply field visibility and field options on our form fields before rendering
     * @return Metizsoft_Reports_Block_Adminhtml_Filter_Form
     */
    protected function _beforeHtml() {
        $result = parent::_beforeHtml();

        $elements = $this->getForm()->getElements();

        // iterate on our elements and select fieldsets
        foreach ($elements as $element) {
            $this->_applyFieldVisibiltyAndOptions($element);
        }

        return $result;
    }

    /**
     * Apply field visibility and options on fieldset element
     * Recursive
     * @param Varien_Data_Form_Element_Fieldset $element
     * @return Varien_Data_Form_Element_Fieldset
     */
    protected function _applyFieldVisibiltyAndOptions($element) {
        if ($element instanceof Varien_Data_Form_Element_Fieldset) {
            foreach ($element->getElements() as $fieldElement) {
                // apply recursively
                if ($fieldElement instanceof Varien_Data_Form_Element_Fieldset) {
                    $this->_applyFieldVisibiltyAndOptions($fieldElement);
                    continue;
                }

                $fieldId = $fieldElement->getId();
                // apply field visibility
                if (!$this->getFieldVisibility($fieldId)) {
                    $element->removeField($fieldId);
                    continue;
                }

                // apply field options
                if (isset($this->_fieldOptions[$fieldId])) {
                    $fieldOptions = $this->_fieldOptions[$fieldId];
                    foreach ($fieldOptions as $k => $v) {
                        $fieldElement->setDataUsingMethod($k, $v);
                    }
                }
            }
        }

        return $element;
    }

}
