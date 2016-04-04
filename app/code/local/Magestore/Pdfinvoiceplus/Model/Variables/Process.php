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
class Magestore_Pdfinvoiceplus_Model_Variables_Process extends Mage_Core_Model_Variable
{

    public function getVariablesOptionArray($type, $withGroup = false)
    {
        //$withGroup = false;
        $collection = $this->getCollection();
        $variables = array();
        $allVars = array();
        foreach ($collection->toOptionArray() as $variable)
        {
            $variables[] = array(
                'value' => '{{customVar code=' . $variable['value'] . '}}',
                'label' => Mage::helper('core')->__('%s', $variable['label'])
            );
        }
        if ($withGroup && $variables)
        {
            $variables = array(
                'label' => Mage::helper('core')->__('Custom Variables'),
                'value' => $variables
            );
        }

        $variableHelper = Mage::helper('pdfinvoiceplus/variable');
        
        $helperCustomer = $variableHelper->getCustomerVariables();
        $helperShipPay = $variableHelper->getShipPayVariables();
        $helperInvoice = $variableHelper->getInvoiceVariablesBarcode();
        $helperOrder = $variableHelper->getOrderVariablesBarcode();
        $helperCreditmemo = $variableHelper->getCreditMemoVariablesBarcode();
        
        if($type == 'order'){
            $allVars = array(
                $helperOrder,
//                $helperShipPay,
                $helperCustomer,
            );
        }elseif($type == 'invoice'){
            $allVars = array(
                $helperInvoice,
//                $helperShipPay,
                $helperCustomer,
            );
        }else{
            $allVars = array(
                $helperCreditmemo,
//                $helperShipPay,
                $helperCustomer,
            );
        }
        return $allVars;
    }

}