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
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
?>
<?php

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

    protected function _prepareMassaction() {
        parent::_prepareMassaction();

        // Append new mass action option 
//        $this->getMassactionBlock()->addItem(
//            'pdfinvoiceplus', array('label' => $this->__('Print Order PDFInvoicePlus'),
//            'url' => $this->getUrl('pdfinvoiceplus/adminhtml_order/printmass') //this should be the url where there will be mass operation
//                )
//        );
    if(Mage::helper('pdfinvoiceplus')->checkEnable()){
         $this->getMassactionBlock()->addItem(
            'pdforder', array('label' => $this->__('Print Orders via PDF Invoice+'),
            'url' => $this->getUrl('pdfinvoiceplusadmin/adminhtml_order/printmassorder') //this should be the url where there will be mass operation
                )
        );
         $this->getMassactionBlock()->addItem(
            'pdfinvoice', array('label' => $this->__('Print Invoices via PDF Invoice+'),
            'url' => $this->getUrl('pdfinvoiceplusadmin/adminhtml_invoice/printmassinvoice') //this should be the url where there will be mass operation
                )
        );
          $this->getMassactionBlock()->addItem(
            'pdfcreditmemo', array('label' => $this->__('Print Credit Memos via PDF Invoice+'),
            'url' => $this->getUrl('pdfinvoiceplusadmin/adminhtml_creditmemo/printmasscreditmemo') //this should be the url where there will be mass operation
                )
        );
    }
    
    }
    

}

