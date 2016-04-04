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


// @codingStandardsIgnoreFile
require_once Mage::getBaseDir('code') . '/core/Mage/Checkout/controllers/CartController.php';

class Mirasvit_Credit_CheckoutController extends Mage_Checkout_CartController
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function applyPostAction()
    {
        $response = $this->_processPost();

        if ($response['success']) {
            $this->_getSession()->addSuccess($response['message']);
        } elseif ($response['message']) {
            $this->_getSession()->addError($response['message']);
        }

        $this->_goBack();
    }

    protected function _processPost()
    {
        $isUseCredit = true;

        if ($this->getRequest()->getParam('remove-credit') == 1) {
            $isUseCredit = false;
        }

        $quote = Mage::getModel('checkout/cart')->getQuote();
        $quote->setUseCredit($isUseCredit)
            ->collectTotals()
            ->save();
    }
}
