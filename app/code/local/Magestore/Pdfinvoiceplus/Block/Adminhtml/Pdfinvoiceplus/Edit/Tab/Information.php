<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tab_Information extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData()) {
            $data = Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData();
            Mage::getSingleton('adminhtml/session')->setPdfinvoiceplusData(null);
        } elseif (Mage::registry('pdfinvoiceplus_data')) {
            $data = Mage::registry('pdfinvoiceplus_data')->getData();
        }

        $fieldset = $form->addFieldset('pdfinvoiceplus_information', array(
            'legend' => Mage::helper('pdfinvoiceplus')->__('General Information')
        ));

        $fieldset->addField('template_name', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Name'),
            'name' => 'template_name',
            'class' => 'required-entry',
            'required' => true,
        ));
        //Format 
        $fieldset->addField('format', 'select', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Paper Size'),
            'name' => 'format',
            'class' => 'required-entry',
            'required' => true,
            'options' => array(
                'Letter' => 'Letter',
                'A4' => 'A4',
                'A5' => 'A5',
                'A6' => 'A6',
                'A7' => 'A7',
            ),
            'onchange' => 'PdfInvoiceTemplate.load()'
            /* 'after_element_html' => '<script type="text/javascript">
              $("fomat").observe("change", showSelectDesign());
              function SelectDesign(){
              PdfInvoiceTemplate.load();
              }
              </script>' */
        ));
        //select localizations
        $localizations = Mage::helper('pdfinvoiceplus/localization')->getList();
        $localizationList = array();
        foreach ($localizations as $localization) {
            $localizationList[$localization['key']] = $localization['value'];
        }
        $fieldset->addField('localization', 'select', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Language'),
            'name' => 'localization',
            'disabled' => $this->getRequest()->getParam('id') ? 'disabled' : '',
            'options' => $localizationList
        ));
        //select design
        $selectDesign = $this->getLayout()
            ->createBlock('adminhtml/widget_button', '', array(
            'type' => 'button',
            'label' => Mage::helper('pdfinvoiceplus')->__('Select Design'),
            'onclick' => 'PdfInvoiceTemplate.load();'
        ));
        $fieldset->addField('preview', 'note', array(
            'text' => $selectDesign->toHtml(),
            'after_element_html' => '<br/>' . Mage::helper('pdfinvoiceplus')->__('To choose and preview a template design that you will use.')
        ));

        $fieldset->addField('system_template_id', 'hidden', array(
            'name' => 'system_template_id'
        ));
//        $fieldset->addField('order_html', 'hidden', array(
//            'name' => 'order_html'
//        ));
//        $fieldset->addField('invoice_html', 'hidden', array(
//            'name' => 'invoice_html'
//        ));
//        $fieldset->addField('creditmemo_html', 'hidden', array(
//            'name' => 'creditmemo_html'
//        ));
        //end filename

        if (Mage::helper('pdfinvoiceplus')->useMultistore()) {
            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name' => 'stores[]',
                    'label' => Mage::helper('pdfinvoiceplus')->__('Store View'),
                    'title' => Mage::helper('pdfinvoiceplus')->__('Store View'),
                    'required' => true,
                    'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                ));
            } else {
                $fieldset->addField('store_id', 'hidden', array(
                    'name' => 'stores[]',
                    'value' => Mage::app()->getStore(true)->getId(),
                ));
            }
        }
        if (!$this->getRequest()->getParam('id'))
            $data['barcode'] = 2;
        $fieldset->addField('barcode', 'select', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Show Barcode'),
            'name' => 'barcode',
            'options' => array(
                1 => 'Yes',
                2 => 'No'
            ),
            'after_element_html' => '<br/>
                                        <a id="barcode_info" href="JavaScript:void(0);">(view example)</a>
                                        <script type="text/javascript">
                                            var tip = new Tooltip("barcode_info","' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'magestore/pdfinvoiceplus/tooltip/barcode.png");
                                        </script>
                                    '
        ));

        $fieldset->addField('barcode_type', 'select', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Barcode Type'),
            'name' => 'barcode_type',
            'options' => array(
                'QR' => 'QR',
                'EAN13' => 'EAN-13',
                'UPCA' => 'UPC-A',
                'UCPE' => 'UCP-E',
                'EAN8' => 'EAN-8',
                'IMB' => 'Intelligent Mail Barcode',
                'RM4SCC' => 'Royal Mail 4-state Customer Barcode',
                'KIX' => 'Royal Mail 4-state Customer Barcode(Dutch)',
                'POSTNET' => 'POSTNET',
                'PLANET' => 'PLANET',
                'C128A' => 'Code 128',
                'EAN128A' => 'EAN-128',
                'C39' => 'Code 39',
                'S25' => 'Standard 2 of 5',
                'C93' => 'Code 93',
                'MSI' => 'MSI',
                'CODABAR' => 'CODABAR',
                'CODE11' => 'Code 11'
            )
        ));
//        if(!$this->getRequest()->getParam('id'))
//            $data['display_images']=2;
//        $fieldset->addField('display_images','select',array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Show Images Product'),
//            'name'  => 'display_images',
//            'options'=> array(
//                1   => 'Yes',
//                2   => 'No'
//            ),
//        ));

        $fieldset->addField('orientation', 'select', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Page Orientation'),
            'name' => 'orientation',
            'class' => 'required-entry',
            'required' => true,
            'options' => array(
                0 => 'Portrait',
                1 => 'Landscape'
            )
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Status'),
            'name' => 'status',
            'class' => 'required-entry',
            'required' => true,
            'options' => array(
                1 => 'Active',
                2 => 'Inactive'
            )
        ));

//        $fieldset->addField('color','text',array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Color'),
//            'name' => 'color',
//            'class' => 'colorpicker',
//            'renderer' => 'pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_renderer_color'
//        ));
//        $form->getElement('color')->setRenderer(
//    		Mage::app()->getLayout()->createBlock(
//		'pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_renderer_color'));
        //company information
//        $fieldset =$form->addFieldSet('pdfinvoiceplus_companyinformation', array(
//            'legend' => Mage::helper('pdfinvoiceplus')->__('Business info <a id="business_info" href="JavaScript:void(0);">(?)</a>'),
//            
//            )); 
//        $fieldset->addField('company_information','note',array(
//            'style' => 'font-weight: bold',
//            'after_element_html'  => '
//                <tr id=""> 
//                    <td class="label" colspan="3">
//                    <div style="margin-top:0px ; font-weight: bold; border-bottom: 1px solid #dfdfdf;">
//                    Company Information
//                    </div>
//                 </td></tr>
//                '
//        ));
//        $fieldset->addField('company_name', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Company Name'),
//            'name' => 'company_name',
//            'class' => 'required-entry',
//            'required' => true,
////            'after_element_html' =>'
////                                     <script stype="text/javascript">
////                                        var tip = new Tooltip("business_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/businessinfo.png");
////                                     </script>
////                                   '
//        ));
//        
//        $fieldset->addField('vat_number', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('VAT Number'),
//            'name' => 'vat_number',
//        ));
//        
//        $fieldset->addField('vat_office', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('VAT Office'),
//            'name' => 'vat_office',
//        ));
//        
//        $fieldset->addField('business_id','text',array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Business ID'),
//            'name'  => 'business_id',
//        ));
//        if($this->getRequest()->getParam('id')){
//            $address = Mage::getModel('pdfinvoiceplus/template')->getCollection()
//            ->addFieldToFilter('template_id',$this->getRequest()->getParam('id'))
//            ->getFirstItem()
//            ->getCompanyAddress();
//        }else{
//            $address = Mage::getStoreConfig('sales/identity/address');
//        } 
//        $fieldset->addField('company_address', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Address'),
//            'name' => 'company_address',
//            'after_element_html'    => '
//                <script type="text/javascript">
//                    $("company_address").value = "'.$address.'";
//                </script>'
//        ));
//        
//        $fieldset->addField('company_logo', 'file', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Logo'),
//            'name' => 'company_logo',
//            'class' => 'input-file',
//            'after_element_html'    => 
//            '<br/><span>
//            Logo, will be used in PDF and HTML documents.
//            <br/>
//            (jpeg, tiff, png) If you see image distortion in PDF, try to use larger image
//            </span>'
//        ));
//        $form->getElement('company_logo')->setRenderer(
//    		Mage::app()->getLayout()->createBlock(
//		'pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_renderer_companylogo'));
//        
//        $fieldset->addField('company_email', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Email'),
//            'name' => 'company_email',
//        ));
//        
//        $fieldset->addField('company_telephone', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Phone'),
//            'name' => 'company_telephone',
//        ));
//        
//        $fieldset->addField('company_fax', 'text', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Fax'),
//            'name' => 'company_fax',
//        ));
        // end company information
        //start term and condition
//        $fieldset->addField('note', 'textarea', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Notes'),
//            'name' => 'note',
//            'style'     => 'height:5em',
//            'after_element_html' =>'<br/>
//                                     <a id="note_info" href="JavaScript:void(0);">(view example)</a>
//                                     <script stype="text/javascript">
//                                        var tip = new Tooltip("note_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/note.png");
//                                     </script>
//                                   '
//        ));
//        $fieldset->addField('terms_conditions','textarea',array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Terms And Conditions'),
//            'name'  => 'terms_conditions',
//            'style'     => 'height:5em',
//            'after_element_html' =>'<br/>
//                                     <a id="terms_info" href="JavaScript:void(0);">(view example)</a>
//                                     <script stype="text/javascript">
//                                        var tip = new Tooltip("terms_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/termscondition.png");
//                                     </script>
//                                   '
//        ));
//        $fieldset->addField('footer', 'textarea', array(
//            'label' => Mage::helper('pdfinvoiceplus')->__('Footer'),
//            'name' => 'footer',
//            'style'     => 'height:5em',
//            'after_element_html' =>'<br/>
//                                     <a id="footer_info" href="JavaScript:void(0);">(view example)</a>
//                                     <script stype="text/javascript">
//                                        var tip = new Tooltip("footer_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/footer.png");
//                                     </script>
//                                   '
//        ));
//        $data['order_html'] = '';//not display
//        $data['invoice_html'] = '';
//        $data['creditmemo_html'] = '';
        $form->setValues($data);
        return parent::_prepareForm();
    }

    public function getVariablesWysiwygActionUrl($type) {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/adminhtml_variable/wysiwygPlugin') . 'type/' . $type;
    }

}

?>
