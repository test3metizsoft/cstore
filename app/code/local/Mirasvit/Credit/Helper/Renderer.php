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



/**
 * Class Mirasvit_Credit_Helper_Renderer
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Mirasvit_Credit_Helper_Renderer extends Mage_Core_Helper_Abstract
{
    public function amountDelta($value, $row, $column)
    {
        if ($row->getData($column->getIndex()) > 0) {
            return '<span style="color:#0a0">+' . $value . '</span>';
        } else {
            return '<span style="color:#f00">' . $value . '</span>';
        }
    }

    public function amount($value, $row, $column)
    {
        if ($row->getData($column->getIndex()) == 0) {
            return '—';
        }

        return $value;
    }

    public function transactionMessage($value, $row, $column)
    {
        return $row->getBackendMessage();
    }
}
