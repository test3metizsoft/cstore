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
class Magestore_Pdfinvoiceplus_Block_Totals_Creditmemo extends Mage_Sales_Block_Order_Creditmemo_Totals
{
    protected function _prepareLayout() {
        parent::__construct();
        $taxBlock = $this->getLayout()->createBlock('pdfinvoiceplus/totals_tax')
                ->setTemplate('pdfinvoiceplus/tax/order/tax.phtml');
        $this->setChild('tax',$taxBlock);
    }
    public function getSource(){
        return $this ->getCreditmemo();
    }
    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if (Mage::registry('current_creditmemo')) {
                $this->_creditmemo = Mage::registry('current_creditmemo');
            } else{
                $this->_creditmemo = $this->getInstance();
                Mage::register('current_creditmemo', $this->_creditmemo);
            }
        }
        return $this->_creditmemo;
    }
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }
}