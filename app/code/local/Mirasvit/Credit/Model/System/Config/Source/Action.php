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



class Mirasvit_Credit_Model_System_Config_Source_Action
{
    public function toOptionArray()
    {
        return array(
            Mirasvit_Credit_Model_Transaction::ACTION_MANUAL   => Mage::helper('credit')->__('Created'),
            Mirasvit_Credit_Model_Transaction::ACTION_REFUNDED => Mage::helper('credit')->__('Refunded'),
            Mirasvit_Credit_Model_Transaction::ACTION_USED     => Mage::helper('credit')->__('Used'),
        );
    }
}
