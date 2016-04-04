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
 * Pdfinvoiceplus Model Entity TotalsRender Order
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer - Tit
 */

class Magestore_Pdfinvoiceplus_Model_Entity_TotalsRender_Creditmemo extends Magestore_Pdfinvoiceplus_Model_Entity_TotalsRender_Abstract
{
    public function __construct() {
        $this->_var_prefix = Magestore_Pdfinvoiceplus_Helper_Variable::_PRE_VAR_CREDITMEMO;
        parent::__construct();
    }
    
    protected function _getBlockTotal(){
        return new Magestore_Pdfinvoiceplus_Block_Adminhtml_Totals_Creditmemo;
    }
    
    protected function _connectTotalCodeVar(){
        $_code_var = array(
            //'adjustment_positive' => "adjustment_positive",
            //'adjustment_negative' => "adjustment_negative"
        );
        foreach($_code_var as $key => $value){
            $this->_totalCodeVars[$key] = '{{var '.$this->_var_prefix.'_'.$value.'}}';
        }
        parent::_connectTotalCodeVar();
        return $this;
    }
}

