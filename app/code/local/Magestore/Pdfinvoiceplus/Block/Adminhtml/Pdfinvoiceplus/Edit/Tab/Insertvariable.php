<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tab_Insertvariable extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $this->setForm($form);
         if (Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData()) {
            $data = Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData();
            Mage::getSingleton('adminhtml/session')->setPdfinvoiceplusData(null);
        } elseif (Mage::registry('pdfinvoiceplus_data')) {
            $data = Mage::registry('pdfinvoiceplus_data')->getData();
        }
         //order name
        $fieldset = $form->addFieldset('pdfinvoiceplus_insertvariable', array(
            'legend' => Mage::helper('pdfinvoiceplus')->__('Variable in PDF Order file')
            ));
        
        if(!$this->getRequest()->getParam('id')){
            $disabled = true;
            $order_filename = 'order_{{var order_increment_id}}_{{var order_created_at}}';
            $invoice_filename = 'invoice_{{var invoice_increment_id}}_{{var invoice_created_at}}';
            $creditmemo_filename = 'creditmemo_{{var creditmemo_increment_id}}';
            $barcode_order = '{{var order_increment_id}}';
            $barcode_invoice = '{{var invoice_increment_id}}';
            $barcode_creditmemo = '{{var creditmemo_increment_id}}';
        }else{
            $order_filename = $data['order_filename'];
            $invoice_filename = $data['invoice_filename'];
            $creditmemo_filename = $data['creditmemo_filename'];
            $barcode_order = $data['barcode_order'];
            $barcode_invoice = $data['barcode_invoice'];
            $barcode_creditmemo = $data['barcode_creditmemo'];
            $disabled = false;
        }
        
        $fieldset->addField('order_filename', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Name to save PDF order'),
            'name' => 'order_filename',
            'class' => 'required-entry',
            'required' => true,
            'after_element_html' => '
                <script type="text/javascript">
                    $("order_filename").value = "'.$order_filename.'";
                </script>'
        ));
        $insertVariableButton = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Insert Variable...'),
            'onclick' => 'MagentovariablePlugin.loadChooser(\'' . $this->getVariablesWysiwygActionUrl('order') . '\', \'order_filename\');'
                ));
        $fieldset->addField('insert_variableorder', 'note', array(
            'text' => $insertVariableButton->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('To save an order with custom name including: customer’s name, date, etc.')
        ));
        //barcode order
        $fieldset->addField('barcode_order','text',array(
            'label' =>Mage::helper('pdfinvoiceplus')->__('Information encoded in Barcode'),
            'name'  => 'barcode_order',
            'after_element_html' => '
                <script type="text/javascript">
                    $("barcode_order").value = "'.$barcode_order.'";
                </script>'
        ));
        $insertVariableButtonBarcode = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Insert Variable...'),
            'onclick' => 'MagentovariablePlugin.loadChooser(\'' . $this->getVariablesWysiwygActionUrl('order') . '\', \'barcode_order\');'
                ));
        $fieldset->addField('insert_variablebarcodeorder', 'note', array(
            'text' => $insertVariableButtonBarcode->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('To choose information encoded in barcode on printed order, including customer’s name, date, etc.')
        ));
        //edit order
        $fieldset->addField('edit_design','hidden',array(
            'name' => 'edit_design'
        ));
        /* Change by Jack 23/12 */
        $editOrders = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Edit Design'),
             'onclick'  => "PdfInvoiceTemplate.editDesign('order')",
            'disabled' => $disabled        
                ));
        $fieldset->addField('is_click_able_order','hidden',array(
            'name'=> 'is_click_able_order',
        ));
        $previewOrder = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Preview Design'),
            'onclick' => "PreviewDesign('order');",
            'disabled' => $disabled   
                ));
        $fieldset->addField('preview_order', 'note', array(
            'text' => $editOrders->toHtml().' '.$previewOrder->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('Note: Firefox or Google Chrome browser is recommended for the best performance of Design Editor.')
        ));
        /* End Change */
        //end order
        //------------------------------------------------------------------------//
        //invoice name
        $fieldsetinvoice = $form->addFieldset('pdfinvoiceplus_insertvariableinvoice', array(
            'legend' => Mage::helper('pdfinvoiceplus')->__('Variable in PDF Invoice file')
            ));
        $fieldsetinvoice->addField('invoice_filename','text',array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Name to save PDF invoice'),
            'name'  => 'invoice_filename',
            'class' => 'required-entry',
            'required' => true,
            'after_element_html' => '
                <script type="text/javascript">
                    $("invoice_filename").value = "'.$invoice_filename.'";
                </script>'
        ));
        $insertVariableButtonInvoice = $this->getLayout()
                                ->createBlock('adminhtml/widget_button','',array(
              'type'    => 'button',
              'label'   => Mage::helper('pdfinvoiceplus')->__('Insert Variable...'),
              'onclick' => 'MagentovariablePlugin.loadChooser(\''.$this->getVariablesWysiwygActionUrl('invoice').'\',\'invoice_filename\', \'invoice\');'                     
         ));
        $fieldsetinvoice->addField('insert_variableinvoice', 'note', array(
            'text' => $insertVariableButtonInvoice->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('To save an invoice with custom name including: customer’s name, date, etc.')
        ));
        //barcode invoice
        $fieldsetinvoice->addField('barcode_invoice','text',array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Information encoded in Barcode'),
            'name'  => 'barcode_invoice',
            'after_element_html' => '
                <script type="text/javascript">
                    $("barcode_invoice").value = "'.$barcode_invoice.'";
                </script>'
        ));
        $insertVariableButtonInvoiceBarcode = $this->getLayout()
                                ->createBlock('adminhtml/widget_button','',array(
              'type'    => 'button',
              'label'   => Mage::helper('pdfinvoiceplus')->__('Insert Variable...'),
              'onclick' => 'MagentovariablePlugin.loadChooser(\''.$this->getVariablesWysiwygActionUrl('invoice').'\',\'barcode_invoice\', \'invoice\');'                     
         ));
        $fieldsetinvoice->addField('insert_variableinvoicebarcode', 'note', array(
            'text' => $insertVariableButtonInvoiceBarcode->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('To choose information encoded in barcode on printed invoice, including customer’s name, date, etc.')
        ));
        //edit and preview Invoice
        /* Change by Jack 23/12 */
        $editInvoice = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Edit Design'),
            'onclick'  => "PdfInvoiceTemplate.editDesign('invoice')",
            'disabled' => $disabled  
                ));
        $fieldsetinvoice->addField('is_click_able_invoice','hidden',array(
            'name'=> 'is_click_able_invoice',
        ));
        $previewInvoice = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Preview Design'),
            'onclick' => "PreviewDesign('invoice');",
            'disabled' => $disabled 
                ));
        $fieldsetinvoice->addField('preview_invoice', 'note', array(
            'text' => $editInvoice->toHtml().' '.$previewInvoice->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('Note: Firefox or Google Chrome browser is recommended for the best performance of Design Editor.')
        ));
        /* End Change */
        //end invoice
        //------------------------------------------------------------------------------//
        //creditmemo name
        $fieldsetcreditmemo = $form->addFieldset('pdfinvoiceplus_insertvariablecreditmemo', array(
            'legend' => Mage::helper('pdfinvoiceplus')->__('Variable in PDF Credit memo file')
            ));
        $fieldsetcreditmemo->addField('creditmemo_filename', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Name to save PDF credit memo'),
            'name' => 'creditmemo_filename',
            'class' => 'required-entry',
            'required' => true,
            'after_element_html' => '
                <script type="text/javascript">
                    $("creditmemo_filename").value = "'.$creditmemo_filename.'";
                </script>'
        ));
        $insertVariableButtonCreditmemo = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Insert Variable...'),
            'onclick' => 'MagentovariablePlugin.loadChooser(\'' . $this->getVariablesWysiwygActionUrl('creditmemo') . '\', \'creditmemo_filename\',\'creditmemo\');'
                ));
        $fieldsetcreditmemo->addField('insert_variablecreditmemo', 'note', array(
            'text' => $insertVariableButtonCreditmemo->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('To save an creditmemo with custom name including: customer’s name, date, etc.')
        ));
        //barcode creditmemo
        $fieldsetcreditmemo->addField('barcode_creditmemo', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Information encoded in Barcode'),
            'name' => 'barcode_creditmemo',
            'after_element_html' => '
                <script type="text/javascript">
                    $("barcode_creditmemo").value = "'.$barcode_creditmemo.'";
                </script>'
        ));
        $insertVariableButtonCreditmemoBarcode = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Insert Variable...'),
            'onclick' => 'MagentovariablePlugin.loadChooser(\'' . $this->getVariablesWysiwygActionUrl('creditmemo') . '\', \'barcode_creditmemo\',\'creditmemo\');'
                ));
        $fieldsetcreditmemo->addField('insert_variablecreditmemobarcode', 'note', array(
            'text' => $insertVariableButtonCreditmemoBarcode->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('To choose information encoded in barcode on printed creditmemo, including customer’s name, date, etc.')
        ));
        
        //end filename
        //edit and preview Creditmemo
        $fieldsetcreditmemo->addField('is_click_able_creditmemo','hidden',array(
            'name'=> 'is_click_able_creditmemo',
        ));
         /* Change by Jack 23/12 */
        $editCreditmemo = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Edit Design'),
            'onclick'  => "PdfInvoiceTemplate.editDesign('creditmemo')",
            'disabled' => $disabled 
                ));
        $previewCreditmemo = $this->getLayout()
                ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Preview Design'),
            'onclick' => "PreviewDesign('creditmemo');",
            'disabled' => $disabled 
        ));
        $fieldsetcreditmemo->addField('preview_creditmemo', 'note', array(
            'text' => $editCreditmemo->toHtml().' '.$previewCreditmemo->toHtml(),
            'after_element_html' => '<br/>'.Mage::helper('pdfinvoiceplus')->__('Note: Firefox or Google Chrome browser is recommended for the best performance of Design Editor.')
        ));
         /* End Change */
        $form->setValues($data);
        return parent::_prepareForm();
    }
    public function getVariablesWysiwygActionUrl($type){
        if($type == 'order'){
            return Mage::getSingleton('adminhtml/url')->getUrl('*/adminhtml_variable/wysiwygPluginOrder');
        }elseif($type == 'invoice'){
            return Mage::getSingleton('adminhtml/url')->getUrl('*/adminhtml_variable/wysiwygPluginInvoice');
        }else{
            return Mage::getSingleton('adminhtml/url')->getUrl('*/adminhtml_variable/wysiwygPluginCreditmemo');
        }
    }
    /* Change by Zeus 02/12 */
    public function getUrlEditOrder()
    {
        return 'setLocation(\'' .$this->getUrl('pdfinvoiceplusadmin/adminhtml_design/editOrder'). '\')';
    }
    public function getUrlEditInvoice()
    {
        return 'setLocation(\'' .$this->getUrl('pdfinvoiceplusadmin/adminhtml_design/editInvoice'). '\')';
    }
    public function getUrlEditCreditmemo()
    {
        return 'setLocation(\'' .$this->getUrl('pdfinvoiceplusadmin/adminhtml_design/editCreditmemo'). '\')';
    }
}
/* End change */
?>
