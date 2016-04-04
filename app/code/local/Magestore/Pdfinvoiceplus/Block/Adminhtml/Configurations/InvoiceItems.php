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
 * Pdfinvoiceplus Configurations Customer Model
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Adminhtml_Configurations_InvoiceItems extends Magestore_Pdfinvoiceplus_Block_Adminhtml_Configurations_Checkbox
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $var_model = Mage::getSingleton('pdfinvoiceplus/variables');
        $all_var = $var_model->getAllVars_Invoice_Items();
        $onHiden_Var = $var_model->getVarsOnHiden_Invoice_Items();
        $mathVar = array();
        foreach($onHiden_Var as $onVar){
            $mathVar[$onVar['value']] = 1;
        }
        $options = array();
        foreach($all_var as $var){
            $options[] = array('value' => $var['value'], 'checked' => (isset($mathVar[$var['value']]))?true:'', 'label' => $var['label']);
        }
        return $options;
    }
}