<?php
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tab_Systemtemplate extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        
        $fieldset = $form->addFieldSet('pdfinvoiceplus_imageshow', array(
           // 'legend' => Mage::helper('pdfinvoiceplus')->__('Select a template to apply'),
            'class' => 'fieldset-wide'
        ));
        
        $fieldset->addField('note', 'note', array()); 
        
        $form->getElement('note')->setRenderer(
    		Mage::app()->getLayout()->createBlock(
		'pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_renderer_imageshow'));
       

    }
}
?>
