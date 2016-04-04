<?php
    class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tab_Companyinfo extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if($this->getRequest()->getParam('id'))
            $disabled = true;
        else
            $disabled = false;
        if (Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData()) {
            $data = Mage::getSingleton('adminhtml/session')->getPdfinvoiceplusData();
            Mage::getSingleton('adminhtml/session')->setPdfinvoiceplusData(null);
        } elseif (Mage::registry('pdfinvoiceplus_data')) {
            $data = Mage::registry('pdfinvoiceplus_data')->getData();
            
        }
        /* Change by Zeus 03/12 */
        if(isset($data['footer_height']) && is_null($data['footer_height']))
            $data['footer_height'] = 40;
        $fieldset =$form->addFieldSet('pdfinvoiceplus_companyinformation', array(
                    'legend' => Mage::helper('pdfinvoiceplus')->__('Default Information'),

                    )); 
         /* End change */
//        $fieldset =$form->addFieldSet('pdfinvoiceplus_companyinformation', array(
//            'legend' => Mage::helper('pdfinvoiceplus')->__('Business info <a id="business_info" href="JavaScript:void(0);">(?)</a>'),
//            
//            )); 
        $business_info = $this ->__('Business Information'); 
        $fieldset->addField('company_information','note',array(
//            'lable' => Mage::helper('pdfinvoiceplus')->__('Content Information'),
            'after_element_html' => '<tr><td class="label" colspan="3"><div style="border-bottom: 1px solid #DFDFDF;font-weight: bold;margin-top: 10px;">'.$business_info.'</div></td></tr>'
        ));
        $fieldset->addField('company_name', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Company Name'),
            'name' => 'company_name',
            'class' => 'required-entry',
            'required' => true,
            'disabled' => $disabled,
//            'after_element_html' =>'
//                                     <script stype="text/javascript">
//                                        var tip = new Tooltip("business_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/businessinfo.png");
//                                     </script>
//                                   '
        ));
        if($this->getRequest()->getParam('id')){
            $address = Mage::getModel('pdfinvoiceplus/template')->getCollection()
            ->addFieldToFilter('template_id',$this->getRequest()->getParam('id'))
            ->getFirstItem()
            ->getCompanyAddress();
        }else{
            $address = Mage::getStoreConfig('sales/identity/address');
        } 
        $fieldset->addField('company_address', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Address'),
            'name' => 'company_address',
            'disabled' => $disabled,
            'after_element_html'    => '
                <script type="text/javascript">
                    $("company_address").value = "'.$address.'";
                </script>'
        ));
        $fieldset->addField('vat_number', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('VAT Number'),
            'name' => 'vat_number',
             'disabled' => $disabled,
        ));
        
        $fieldset->addField('vat_office', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('VAT Office'),
            'name' => 'vat_office',
            'disabled' => $disabled,
        ));
        
        $fieldset->addField('business_id','text',array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Business ID'),
            'name'  => 'business_id',
            'disabled' => $disabled,
        ));
        $fieldset->addField('company_logo', 'file', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Logo'),
            //'name' => 'company_logo',
            //'class' => 'input-file',
            //'after_element_html'    => 
            //'<br/><span>
            //Logo, will be used in PDF and HTML documents.
            //<br/>
            //(jpeg, tiff, png) If you see image distortion in PDF, try to use larger image
            //</span>'
        ));
        $form->getElement('company_logo')->setRenderer(
    		Mage::app()->getLayout()->createBlock(
		'pdfinvoiceplus/adminhtml_pdfinvoiceplus_edit_tab_renderer_companylogo'));
        
//        Business Contact
        $business_contact = $this ->__('Business Contact'); 
        $fieldset->addField('content_information','note',array(
             'disabled' => $disabled,
            'after_element_html' => '<tr><td class="label" colspan="3"><div style="border-bottom: 1px solid #DFDFDF;font-weight: bold;margin-top: 10px;">'.$business_contact.'</div></td></tr>'
        ));
        $fieldset->addField('company_email', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Email'),
            'name' => 'company_email',
            'disabled' => $disabled,
            'class' => 'input-text validate-email',
        ));
        
        $fieldset->addField('company_telephone', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Phone'),
            'name' => 'company_telephone',
            'disabled' => $disabled,
            'class' => 'input-text validate-number',
        ));

        $fieldset->addField('company_fax', 'text', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Fax'),
            'name' => 'company_fax',
            'disabled' => $disabled,
        ));
        //Additional Information
        $additional = $this ->__('Additional Information'); 
        $fieldset->addField('contact_information','note',array(
            'disabled' => $disabled,
            'after_element_html' => '<tr><td class="label" colspan="3"><div style="border-bottom: 1px solid #DFDFDF;font-weight: bold;margin-top: 10px;">'.$additional.'</div></td></tr>'
        ));
        $fieldset->addField('note', 'textarea', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Notes'),
            'name' => 'note',
            'style'     => 'height:5em',
            'disabled' => $disabled,
            'after_element_html' =>'<br/>
                                     <a id="note_info" href="JavaScript:void(0);">(view example)</a>
                                     <script stype="text/javascript">
                                        var tip = new Tooltip("note_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/note.png");
                                     </script>
                                   '
        ));
        $fieldset->addField('terms_conditions','textarea',array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Terms and Conditions'),
            'name'  => 'terms_conditions',
            'style'     => 'height:5em',
            'disabled' => $disabled,
            'after_element_html' =>'<br/>
                                     <a id="terms_info" href="JavaScript:void(0);">(view example)</a>
                                     <script stype="text/javascript">
                                        var tip = new Tooltip("terms_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/termscondition.png");
                                     </script>
                                   '
        ));
        $fieldset->addField('footer', 'textarea', array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Footer'),
            'name' => 'footer',
            'disabled' => $disabled,
            'style'     => 'height:5em',
            'after_element_html' =>'<br/>
                                     <a id="footer_info" href="JavaScript:void(0);">(view example)</a>
                                     <script stype="text/javascript">
                                        var tip = new Tooltip("footer_info","'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/tooltip/footer.png");
                                     </script>
                                   '
        ));
        $fieldset->addField('footer_height', 'text', array(
            'disabled' => $disabled,
            'label' => Mage::helper('pdfinvoiceplus')->__('Footer Height'),
            'name' => 'footer_height',
            'note'=>'px',
            'class'=>'input-text validate-number',
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }
}

