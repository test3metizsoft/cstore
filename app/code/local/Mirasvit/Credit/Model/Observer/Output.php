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



class Mirasvit_Credit_Model_Observer_Output
{
    public function afterOutput($obj)
    {
        $block = $obj->getEvent()->getBlock();
        $transport = $obj->getEvent()->getTransport();

        if (empty($transport)) {
            return $this;
        }

        $this->appendCartCreditBlock($block, $transport);

        return $this;
    }

    protected $isBlockInserted = false;

    public function appendCartCreditBlock($block, $transport)
    {
        if (Mage::app()->getRequest()->getControllerName() != 'cart') {
            return $this;
        }
        if ($block->getBlockAlias() == 'coupon'
            || $block->getBlockAlias() == 'crosssell'
        ) {
            $b = $block->getLayout()
                ->createBlock('credit/checkout_cart_credit', 'credit')
                ->setTemplate('mst_credit/checkout/cart/credit.phtml');

            $html = $transport->getHtml();
            $ourhtml = $b->toHtml();
            if (!$this->isBlockInserted) {
                $html = $ourhtml . $html;
                $this->isBlockInserted = true;
            }
            $transport->setHtml($html);
        }
        return $this;
    }
}
