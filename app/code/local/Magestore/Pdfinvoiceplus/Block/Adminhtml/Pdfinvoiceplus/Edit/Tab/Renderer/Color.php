<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Edit_Tab_Renderer_Color extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        if (!$this->getRequest()->getParam('id')) {
            //$logo = Mage::getStoreConfig('sales/identity/logo');
            $html = '
            <td class="label"><label for="company_logo"> Choose template\'s color </label></td>
            <td class="value">
                <input id="color" name="color" style="display:none" value="" class="colorpicker input-text" disabled="disabled" type="text" autocomplete="off" style="background-image: none; background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">
                <br>
                <input id="use_default_color" type="checkbox" checked onclick="resetDefault(this)" name="default_color">
                <label>Use the default color of the template selected in tab “Template”</label>
                <script type="text/javascript">
                    function resetDefault(el){
                        if(el.checked){
                            $("color").value = "";
                            $("color").style.backgroundColor = "#fff";
                            $("color").disabled = true;
                        }else{
                            $("color").disabled = false;
                            if($("color").style.display == "none")
                                $("color").style.display = "";
                        }
                    }
                </script>            
                </td>';
        } else {
            $default = '';
            $disabled = '';
            $class = 'color';
            $color = $element->getValue();
            $display = 'blocl';
            if($color == ''){
                $default = 'checked';
                $disabled = 'disabled';
                $display = 'none';
                $class = '';
            }
            $html = '
                <td class="label"><label for="company_logo"> Choose template\'s color </label></td>
                <td class="value">
                <input '.$disabled.' style="display:'.$display.'" id="color" name="color" class="color input-text" value="'.$color.'" />
                <br>
                <input id="use_default_color" type="checkbox" '.$default.' onclick="resetDefault(this)" name="default_color" />
                <label>Use the default color of the template selected in tab “Template”</label>
                <script type="text/javascript">
                    function resetDefault(el){
                        if(el.checked){
                            
                            $("color").value = "";
                            $("color").style.backgroundColor = "#fff";
                            $("color").disabled = true;
                        }else{
                            if($("color").style.display == "none")
                                $("color").style.display = "";
                            $("color").disabled = false;
                        }
                    }
                </script>
                </td>';
        }
        return $html;
    }

}

?>
