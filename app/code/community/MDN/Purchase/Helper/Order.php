<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Purchase_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * Create new order
     */
    public function createNewOrder($supplierId) {

        $supplier = Mage::getModel('Purchase/Supplier')->load($supplierId);

        //define currency
        $currency = Mage::getStoreConfig('purchase/purchase_order/default_currency');
        if ($supplier->getsup_currency())
                $currency = $supplier->getsup_currency();

        //define tax rate
        $taxRate = Mage::getStoreConfig('purchase/purchase_order/default_shipping_duties_taxrate');
        if ($supplier->getTaxRate()->getptr_value() > 0)
                $taxRate = $supplier->getTaxRate()->getptr_value();

        //create order
        $model = mage::getModel('Purchase/Order');
        $order = $model
                        ->setpo_sup_num($supplierId)
                        ->setpo_date(date('Y-m-d'))
                        ->setpo_currency($currency)
                        ->setpo_tax_rate($taxRate)
                        ->setpo_order_id($model->GenerateOrderNumber())
                        ->setpo_status('new')
                        ->save();
        return $order;
    }

}