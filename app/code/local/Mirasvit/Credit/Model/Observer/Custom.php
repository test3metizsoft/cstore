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



class Mirasvit_Credit_Model_Observer_Custom extends Mirasvit_Credit_Model_Observer_Abstract
{
    public function gomageOnPredispatch()
    {
        $params = Mage::app()->getRequest()->getParams();

        if (isset($params['credit'])) {
            $isUseCredit = (bool)$params['credit'];

            $quote = Mage::getModel('checkout/cart')->getQuote();
            $quote->setUseCredit($isUseCredit)
                ->collectTotals()
                ->save();
        }
    }

    public function lotusbreathOnPredispatch()
    {
        $params = Mage::app()->getRequest()->getParams();

        if (isset($params['credit'])) {
            $isUseCredit = (bool)$params['credit'];

            $quote = Mage::getModel('checkout/cart')->getQuote();
            $quote->setUseCredit($isUseCredit)
                ->collectTotals()
                ->save();
        }
    }
}