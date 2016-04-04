<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdfinvoiceplus_Renderer_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $id = $element->getHtmlId();
        $html  = '<tr id="row_' . $id . '">'
                . '<td class="label" colspan="3">';
        $marginTop = $element->getComment() ? $element->getComment() : '0px';
        $html .= '<div style="margin-top: ' . $marginTop
                . '; font-weight: bold; border-bottom: 1px solid #dfdfdf;">';
        $html .= $element->getLabel();
        $html .= '</div></td></tr>';
        return $html;
    }

}

?>
