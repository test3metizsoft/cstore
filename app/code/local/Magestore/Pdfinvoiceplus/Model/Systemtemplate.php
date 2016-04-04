<?php

class Magestore_Pdfinvoiceplus_Model_Systemtemplate extends Mage_Core_Model_Abstract {
    protected $_type = 'invoice';
    public function _construct() {
        parent::_construct();
        $this->_init('pdfinvoiceplus/systemtemplate');
    }
    public function setType($type){
        $this->_type = $type;
    }
    
    public function getTemplateBody($type){
        if($this->getId()){
            $code = $this->getCode();
            $block = new Magestore_Pdfinvoiceplus_Block_Adminhtml_Pdf();
            $html = $block  
                    ->setSource($this->getSource())
                    ->setTemplate('pdfinvoiceplus/templates/'.$code.'/'.$type.'.phtml')
                    ->toHtml();
            return $html;
        }
        return '';
    }

}

?>
