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
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Widget_Form_Renderer_Fieldset extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset
{
    //protected $_element;
    public function _construct()
    {
        parent::_construct();
        //$this->setTemplate('pdfinvoiceplus/fieldset.phtml');
    }
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        if($element->getId() == 'pdfinvoiceplus_information')
            $this->setTemplate('pdfinvoiceplus/fieldset.phtml');
        return $this->toHtml();
    }
}