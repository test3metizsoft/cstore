<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tab_Renderer_Companylogo extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface {
/* Change by Zeus 03/12 */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        if ($this->getRequest()->getParam('id') == '') {
            //$logo = Mage::getStoreConfig('sales/identity/logo');
            /* Change by Zeus 03/12 */
            $html = '
            <td class="label"><label for="company_logo"> Logo </label></td>
            <td class="value">
            <!--img width="22" height="22" class="small-image-preview v-middle" alt="" title="" id="company_logo_image" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'magestore/pdfinvoiceplus/logo/notshow.png"-->
            <input type="file" class="input-file" value="" name="company_logo" id="company_logo">
            <span class="delete-image">
            <input type="checkbox" id="company_logo_delete" class="checkbox" value="1" name="company_logo_delete"><label for="company_logo_delete"> Delete Image</label>
            <input type="hidden" value="" name="company_logo"><br/>
            Recommended image size: <strong>160x40</strong> pixels.<br/>
            jpeg, tiff, png files supported..
            </td>';
            /* End change */
        } else {
		
            $logo = Mage::getModel('pdfinvoiceplus/template')->load($this->getRequest()->getParam('id'))
                ->getCompanyLogo();
				//zend_debug::dump($logo); die('vao day');
				if(!$logo){
						$html = '
            <td class="label"><label for="company_logo"> Logo </label></td>
            <td class="value">
            <!--img width="22" height="22" class="small-image-preview v-middle" alt="' . $logo . '" title="' . $logo . '" id="company_logo_image" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'magestore/pdfinvoiceplus/logo/notshow.png"-->
            <input type="file" class="input-file" disabled="disabled" value="" name="company_logo" id="company_logo">
            <span class="delete-image">
            <input type="checkbox" id="company_logo_delete" class="checkbox" value="1" name="company_logo_delete" disabled><label for="company_logo_delete" > Delete Image</label>
            <input type="hidden" value="' . $logo . '" name="company_logo"><br/>
            Recommended image size: <strong>160x40</strong> pixels.<br/>
            jpeg, tiff, png files supported..
            </td>';
					}else{
							$html = '
            <td class="label"><label for="company_logo"> Company Logo </label></td>
            <td class="value">
            <img width="22" height="22" class="small-image-preview v-middle" alt="' . $logo . '" title="' . $logo . '" id="company_logo_image" src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).  'magestore/pdfinvoiceplus/logo/'.$logo.'">
            <input type="file" class="input-file" value="" disabled="disabled" name="company_logo" id="company_logo">
            <span class="delete-image">
            <input type="checkbox" id="company_logo_delete" class="checkbox" value="1" name="company_logo_delete" disabled><label for="company_logo_delete"> Delete Image</label>
            <input type="hidden" value="' . $logo . '" name="company_logo_hidden"><br/>
            The most idea logo size is <strong>160x40</strong> pixels.<br/>
            Logo will be used in PDF and HTML document (jpeg, tiff, png).
            </td>';
					}
            
        }
        return $html;
    }
/* End change */
}

?>
