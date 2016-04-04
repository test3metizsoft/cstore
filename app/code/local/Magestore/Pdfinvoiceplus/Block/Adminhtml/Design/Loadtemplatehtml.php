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
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Design_Loadtemplatehtml extends Mage_Core_Block_Template {
    protected $_orderHtml = '';
    protected $_invoiceHtml = '';
    protected $_creditmemoHtml = '';
    protected $_id;
    public function __construct() {
        $model = Mage::registry('pdfinvoiceplus_data');
        $this->_id = $model->getId();
        $this->_orderHtml = $model->getOrderHtml();
        $this->_invoiceHtml = $model->getInvoiceHtml();
        $this->_creditmemoHtml = $model->getCreditmemoHtml();
        parent::__construct();
    }
}
