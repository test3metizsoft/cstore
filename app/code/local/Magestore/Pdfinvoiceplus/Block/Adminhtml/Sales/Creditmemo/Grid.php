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

class Magestore_Pdfinvoiceplus_Block_Adminhtml_Sales_Creditmemo_Grid extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid {

    protected function _prepareMassaction() {
        parent::_prepareMassaction();
        if(Mage::helper('pdfinvoiceplus')->checkEnable()){
         $this->getMassactionBlock()->addItem(
            'pdfcreditmemogrid', array('label' => $this->__('Print Credit Memos via PDF Invoice+'),
            'url' => $this->getUrl('pdfinvoiceplusadmin/adminhtml_creditmemo/printmasscreditmemogrid') //this should be the url where there will be mass operation
                )
        );

    }
    
    }

}

