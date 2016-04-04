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
 * Pdfinvoiceplus Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Totals_Creditmemo extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals {

    public function __construct() {
        $this->_beforeToHtml();
        parent::_construct();
    }

    public function getSource(){
        return Mage::registry('source_totals');
    }
    
//    protected function _prepareLayout(){
//        parent::__construct();
//        $totalBlock = $this -> getLayout() ->createBlock('pdfinvoiceplus/adminhtml_totals_tax')
//                            ->setTemplate('pdfinvoiceplus/sales/totals/tax.phtml');
//        $this ->setChild('tax',$totalBlock);
//    }
//    public function getSource(){
//        return $this->getCreditmemo();
//    }
//    public function getCreditmemo()
//    {
//        if ($this->_creditmemo === null) {
//            if (Mage::registry('current_creditmemo')) {
//                $this->_creditmemo = Mage::registry('current_creditmemo');
//            } else{
//                $this->_creditmemo = $this->getInstance();
//                Mage::register('current_creditmemo', $this->_creditmemo);
//            }
//        }
//        return $this->_creditmemo;
//    }
//    public function getOrder()
//    {
//        return $this->getCreditmemo()->getOrder();
//    }
}
