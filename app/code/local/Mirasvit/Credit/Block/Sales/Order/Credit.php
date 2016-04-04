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



class Mirasvit_Credit_Block_Sales_Order_Credit extends Mage_Core_Block_Template
{
    public function initTotals()
    {
        if (floatval($this->getSource()->getCreditAmount()) == 0) {
            return $this;
        }

        $total = new Varien_Object(array(
            'code'       => $this->getNameInLayout(),
            'block_name' => $this->getNameInLayout(),
            'area'       => $this->getArea()
        ));

        $this->getParentBlock()->addTotal($total, $this->getAfterTotal());

        return $this;
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
