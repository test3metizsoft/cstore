<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Store Credit & Refund
 * @version   1.0.0
 * @build     307
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Credit_Block_Adminhtml_Sales_Order_Creditmemo_Controls extends Mage_Core_Block_Template
{
    public function canRefundToCredit()
    {
        if (Mage::registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }

        return true;
    }

    public function getReturnValue()
    {
        $max = Mage::registry('current_creditmemo')->getCreditReturnMax();

        if ($max) {
            return $max;
        }

        return 0;
    }
}
