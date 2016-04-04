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



class Mirasvit_Credit_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isAutoRefundEnabled()
    {
        return true;
    }

    public function createTransactionMessage($array)
    {
        $arMessage = array();

        if (isset($array['order'])) {
            $order = $array['order'];

            $arMessage[] = Mage::helper('credit')->__('Order #%s', 'o|' . $order->getIncrementId());
        }

        if (isset($array['creditmemo'])) {
            $memo = $array['creditmemo'];

            $arMessage[] = Mage::helper('credit')->__('Creditmemo #%s', 'm|' . $memo->getIncrementId());
        }

        return implode(', ', $arMessage);
    }

    public function getBackendTransactionMessage($message)
    {
        return $this->getPreparedTransactionMessage($message, 'adminhtml');
    }

    public function getFrontendTransactionMessage($message)
    {
        return $this->getPreparedTransactionMessage($message, 'frontend');
    }

    public function getEmailTransactionMessage($message)
    {
        return $this->getPreparedTransactionMessage($message, 'email');
    }

    public function getPreparedTransactionMessage($message, $type)
    {
        $message = $this->highlightOrdersInMessage($message, $type);
        $message = $this->highlightMemosInMessage($message, $type);

        return $message;
    }

    protected function highlightOrdersInMessage($message, $type)
    {
        $orderMatches = array();
        preg_match_all('/#o\|([0-9]*)/is', $message, $orderMatches);

        if (count($orderMatches) && isset($orderMatches[1])) {
            foreach ($orderMatches[1] as $key => $incrementId) {
                $order = Mage::getModel('sales/order')->getCollection()
                    ->addFieldToFilter('increment_id', $incrementId)
                    ->getFirstItem();

                $url = false;
                if ($type == 'adminhtml') {
                    $url = Mage::helper('adminhtml')->getUrl(
                        'adminhtml/sales_order/view',
                        array('order_id' => $order->getId())
                    );
                } elseif ($type == 'frontend') {
                    $url = Mage::getUrl('sales/order/view', array('order_id' => $order->getId()));
                }

                if ($url) {
                    $replace = "<a href='$url' target='_blank'>#$incrementId</a>";
                } else {
                    $replace = "#$incrementId";
                }

                $message = str_replace($orderMatches[0][$key], $replace, $message);
            }
        }

        return $message;
    }

    protected function highlightMemosInMessage($message, $type)
    {
        $memoMatches = array();
        preg_match_all('/#m\|([0-9]*)/is', $message, $memoMatches);

        if (count($memoMatches) && isset($memoMatches[1])) {
            foreach ($memoMatches[1] as $key => $incrementId) {
                $memo = Mage::getModel('sales/order_creditmemo')->getCollection()
                    ->addFieldToFilter('increment_id', $incrementId)
                    ->getFirstItem();

                $url = false;
                if ($type == 'adminhtml') {
                    $url = Mage::helper('adminhtml')->getUrl(
                        'adminhtml/sales_creditmemo/view',
                        array('creditmemo_id' => $memo->getId())
                    );
                } elseif ($type == 'frontend') {
                    $url = Mage::getUrl('sales/order/creditmemo', array('order_id' => $memo->getOrderId()));
                }

                if ($url) {
                    $replace = "<a href='$url' target='_blank'>#$incrementId</a>";
                } else {
                    $replace = "#$incrementId";
                }

                $message = str_replace($memoMatches[0][$key], $replace, $message);
            }
        }

        return $message;
    }
}
