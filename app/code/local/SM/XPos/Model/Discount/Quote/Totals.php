<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/3/15
 * Time: 2:11 PM
 */

class SM_XPos_Model_Discount_Quote_Totals extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    const DISCOUNT_DESCRIPTION = 'XPos Discount';

    /** @var Mage_Sales_Model_Quote_Address $_quoteAddress */
    protected $_quoteAddress;

    protected static $_baseDiscount;

    protected static $_discount;

    protected static $_discountDescription;

    /**
     * Observe Event sales_quote_address_collect_totals_before
     * In Mage_Sales_Model_Quote_Address::collectTotals()
     *
     * @param $eventObject
     */
    public function xposDiscountQuoteTotalsBefore($eventObject)
    {
        $this->_quoteAddress = $eventObject->getQuoteAddress();
        if ($this->_quoteAddress->getData('address_type') == 'billing') {
            return;
        }
        $discount = Mage::app()->getRequest()->getPost('xpos_discount');
        if (!is_null($discount)) {
            if ($this->currentQuoteHasCouponCode()) {
                $this->currentQuoteRemoveCouponCode();
            }
        } else {
            if (!$this->currentQuoteHasCouponCode()) {
                self::$_baseDiscount = $this->_quoteAddress->getOrigData('base_discount_amount');
                self::$_discount = $this->_quoteAddress->getOrigData('discount_amount');
                self::$_discountDescription = $this->_quoteAddress->getOrigData('discount_description');
            }
        }
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_quoteAddress = $address;
        if ($this->_quoteAddress->getData('address_type') == 'billing') {
            return;
        }
        $discount = Mage::app()->getRequest()->getPost('xpos_discount');
        if (!is_null($discount)) {
            $this->_quoteAddress->addTotalAmount('discount',-floatval($discount));
            $this->_quoteAddress->addBaseTotalAmount('discount',-floatval($discount));
            $this->_quoteAddress->setDiscountDescription(self::DISCOUNT_DESCRIPTION);
        } else {
            if (!$this->currentQuoteHasCouponCode() &&
                self::$_discountDescription == self::DISCOUNT_DESCRIPTION) {
                $this->resetXposDiscount();
            }
        }
    }

    protected function currentQuoteHasCouponCode()
    {
        return ((bool) $this->_quoteAddress->getQuote()->getCouponCode());
    }

    protected function currentQuoteRemoveCouponCode()
    {
        $this->_quoteAddress->getQuote()->setCouponCode('');
    }

    protected function resetXposDiscount()
    {
        $this->_quoteAddress->addTotalAmount('discount',floatval(self::$_discount));
        $this->_quoteAddress->addBaseTotalAmount('discount',floatval(self::$_baseDiscount));
        $this->_quoteAddress->setDiscountDescription(self::DISCOUNT_DESCRIPTION);
    }

}