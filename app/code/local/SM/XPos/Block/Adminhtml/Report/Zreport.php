<?php

/**
 * Created by PhpStorm.
 * User: Smartor
 * Date: 10/15/14
 * Time: 9:39 AM
 */
class SM_XPos_Block_Adminhtml_Report_Zreport extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        $this->setTemplate('sm/xpos/report/zreport.phtml');
        parent::_construct();
    }

    public function getTillId()
    {
        $till_id = $this->getRequest()->getParam('till_id');
        Mage::getSingleton('adminhtml/session')->setTillInfo($till_id);
        return $till_id;
    }

    public function getTillName()
    {
        $till_id = $this->getRequest()->getParam('till_id');
        $tillName = Mage::getModel('xpos/till')->load($till_id)->getData('till_name');
        Mage::getSingleton('adminhtml/session')->setTillInfo($till_id);
        return $tillName;
    }

    public function getPaymentPaidInfo()
    {
        $data = array();

        if (Mage::helper('xpos/configXPOS')->getEnableTill() === '1') {
            $till_id = $this->getRequest()->getParam('till_id');
        } else {
            $till_id = NULL;
        }

        if (Mage::helper('xpos/configXPOS')->getIntegrateXmwhEnable()) {
            $warehouse_id = Mage::getSingleton('admin/session')->getWarehouseId();
            Mage::getSingleton('adminhtml/session')->setWarehouseReport($warehouse_id);
        } else $warehouse_id = NULL;

        ($till_id === NULL) ? $report_collec = $this->_getReportCollection(0) : $report_collec = $this->_getReportCollection($till_id);

        if (count($report_collec) > 0) {
            $fist_item = $report_collec->getFirstItem();
            $previous_time = $fist_item->getData('created_time');
        } else $previous_time = '2014-01-01 01:01:01';
        if ($till_id !== NULL)
            $collection = $this->_getXPosOrderCollection($till_id, $previous_time);
        else
            $collection = $this->_getXPosOrderCollection(null, $previous_time);

        $data['other_payment']['num_order_total'] = count($collection);
        $data['other_payment']['previous_time'] = $previous_time;
        $data['other_payment']['grand_order_total'] = 0;
        $data['other_payment']['tax_order_total'] = 0;
        $data['other_payment']['total_refund'] = 0;
        $payment_arr = array();
        $data['other_payment']['money_system'] = 0;
        $data['other_payment']['order_count'] = 0;
        $data['other_payment']['transac_in'] = 0;
        $data['other_payment']['transac_out'] = 0;

        //Get transaction info of day
        $default_transfer = Mage::getStoreConfig('xpos/report/default_transfer');
        //getReal Current balance of till
        if ($till_id === NULL) {
            $transacs = $this->_getXPosTransacCollection(0, $previous_time);
        } else {
            $transacs = $this->_getXPosTransacCollection($till_id, $previous_time);
        }

        $real_current_balance = $transacs->getFirstItem()->getData('current_balance');
        $previous_transfer = $transacs->getLastItem()->getData('current_balance');

        if ($till_id === NULL) {
            $transac_collection = $this->_getManualXPosTransacCollection(0, $previous_time);
        } else {
            $transac_collection = $this->_getManualXPosTransacCollection($till_id, $previous_time);
        }

        $current_balance = $transac_collection->getFirstItem()->getData('current_balance');

        foreach ($transac_collection as $transaction) {
            if ($transaction->getData('type') == 'in') {
                $data['other_payment']['transac_in'] += floatval($transaction->getData('cash_in'));
            } else {
                $data['other_payment']['transac_out'] += floatval($transaction->getData('cash_out'));
            }
        }

        if ($previous_time !== '2014-01-01 01:01:01')
            $amount_diff = $data['other_payment']['transac_in'] - $data['other_payment']['transac_out'] - $previous_transfer;
        else {
            $amount_diff = $data['other_payment']['transac_in'] - $data['other_payment']['transac_out'];
            $previous_transfer = 0;
        }

        $grandTotalOrder = 0;
        $data['split_total'] = 0;
        // caculate Amount
        $allowSplitPaymentList = array_keys(Mage::getStoreConfig('xpayment'));
        $otherPaymentList = array('ccsave', 'authorizenet', 'cashondelivery', 'checkmo');
        $cashGroup = array('xpayment_cashpayment', 'cashondelivery');
        $ccGroup = array('ccsave', 'xpayment_ccpayment', 'authorizenet');
        $allowPaymentList = array_merge($allowSplitPaymentList, $otherPaymentList);
        if (count($collection) > 0)
            foreach ($collection as $order) {

                $grandTotal = $order->getData('base_grand_total');
                $data['other_payment']['grand_order_total'] += floatval($grandTotal);
                $data['other_payment']['tax_order_total'] += floatval($order->getData('base_tax_amount'));
                if ($order->getData('xpos_app_order_id') !== NULL)
                    $payment = 'ipadorder';
                else
                    $payment = $order->getPayment()->getMethod();


                if (
                    in_array($payment, $allowPaymentList)
                    || Mage::helper('xpos/configXPOS')->getShowAdditionInEndOfDayReport()
                    || $payment === 'xpaymentMultiple'
                ) {
                    $hasSplit = false;
                    $splitData = array();
                    switch ($payment) {
                        case 'xpaymentMultiple':
                            $hasSplit = true;
                            break;
                        default:
                            $data[$payment]['payment_name'] = $paymentTitle = Mage::getStoreConfig('payment/' . $payment . '/title');
                            break;
                    }

                    if (!$hasSplit) {
//                        if (in_array($payment, $cashGroup)) {
//                            $data['xpayment_cashpayment']['money_system'] += floatval($grandTotal);
//                            $data['xpayment_cashpayment']['order_count'] ? $data['xpayment_cashpayment']['order_count'] += 1 : $data['xpayment_cashpayment']['order_count'] = 1;
//                        }
//                        if (in_array($payment, $ccGroup)) {
//                            $data['xpayment_ccpayment']['money_system'] += floatval($grandTotal);
//                            $data['xpayment_ccpayment']['order_count'] ? $data['xpayment_ccpayment']['order_count'] += 1 : $data['xpayment_ccpayment']['order_count'] = 1;
//                        }
//                        if (!in_array($payment, $ccGroup) && !in_array($payment, $cashGroup)) {
                        isset($data[$payment]['money_system']) ? $data[$payment]['money_system'] += floatval($grandTotal) : $data[$payment]['money_system'] = floatval($grandTotal);
                        isset($data[$payment]['order_count']) ? $data[$payment]['order_count'] += 1 : $data[$payment]['order_count'] = 1;
                        $grandTotalOrder += $grandTotal;
//                        }
                    } else {
//                        Zend_Debug::dump(floatval($grandTotal));
                        $arrayNotShow = array('method', 'enable', 'check_no', 'check_date', 'checks');
                        $details = @unserialize($order->getPayment()->getAdditionalData());
                        if ($details !== null) {
                            $splitData = $details;
                        }

                        $splitAmount = 0;
                        foreach ($splitData as $k => $v) {
                            if (in_array($k, $arrayNotShow)) {
                                continue;
                            }
////                            var_dump($k);
//                            if (in_array($k, $cashGroup)) {
//                                $data['xpayment_cashpayment']['payment_name'] = 'Cash';
//                                $data['xpayment_cashpayment']['money_system'] += floatval($v);
//                                $data['xpayment_cashpayment']['order_count'] ? $data['xpayment_cashpayment']['order_count'] += 1 : $data['xpayment_cashpayment']['order_count'] = 1;
//                                continue;
//                            }
//                            if (in_array($k, $ccGroup)) {
//                                $data['xpayment_ccpayment']['payment_name'] = 'Credit Card';
//                                $data['xpayment_ccpayment']['money_system'] += floatval($v);
//                                $data['xpayment_ccpayment']['order_count'] ? $data['xpayment_ccpayment']['order_count'] += 1 : $data['xpayment_ccpayment']['order_count'] = 1;
//                                continue;
//                            }
                            $splitAmount += floatval($v);
                            $data[$k]['payment_name'] = Mage::getStoreConfig('payment/' . $k . '/title');;
                            isset($data[$k]['money_system']) ? $data[$k]['money_system'] += floatval($v) : $data[$k]['money_system'] = floatval($v);
                            isset($data[$k]['order_count']) ? $data[$k]['order_count'] += 1 : $data[$k]['order_count'] = 1;
                        }
                        if ($splitAmount > $grandTotal) {
                            $v = $grandTotal - $splitAmount;
                            isset($data['xpayment_cashpayment']['money_system'])?$data['xpayment_cashpayment']['money_system'] += $v:$data['xpayment_cashpayment']['money_system'] = $v;
                        }
                        isset($data['split_total'])?$data['split_total'] += $grandTotal:$data['split_total'] = $grandTotal;
                    }
                } else {
                    $data['other_payment']['payment_name'] = 'Other Payments';
                    $data['other_payment']['money_system'] += floatval($order->getData('base_grand_total'));
                    $data['other_payment']['order_count']++;
                    $grandTotalOrder += $grandTotal;
                }
            }

        //calculate refund amount base on order update time
        if ($till_id !== NULL)
            $refundCollection = $this->_getOrderCollection($till_id, $previous_time);
        else
            $refundCollection = $this->_getOrderCollection(null, $previous_time);

        if (count($refundCollection) > 0) {
            foreach ($refundCollection as $order) {
                $data['other_payment']['total_refund'] += floatval($order->getData('base_total_refunded'));
            }
        }

        //Count cash(transfer info)
        $total = $current_balance;
        if (isset($data['xpayment_cashpayment']['money_system'])) {
            $total = $current_balance + $data['xpayment_cashpayment']['money_system'];
        }

        $result = $total + $amount_diff;
        $data['xpayment_cashpayment']['payment_name'] = 'Cash';
        $data['xpayment_cashpayment']['money_system'] = $result;
        $data['xpayment_cashpayment']['total'] = $total;
        $data['xpayment_cashpayment']['in_out'] = $amount_diff;
        $data['xpayment_cashpayment']['previous_transfer'] = $previous_transfer;

//        $data['other_payment']['grand_order_total'] += $previous_transfer + $amount_diff;
        $data['other_payment']['grand_order_total'] = $grandTotalOrder;
        $data['other_payment']['till_current'] = $real_current_balance;

        // xpayment_cashpayment should be the last in list
        $cashpayment = $data['xpayment_cashpayment'];
        unset($data['xpayment_cashpayment']);
        $data['xpayment_cashpayment'] = $cashpayment;

        Mage::getSingleton('adminhtml/session')->setPaymentInfo($data);
        return $data;
    }

    public function getDiscountPaidInfo()
    {
        $report_collec = Mage::getModel('xpos/report')->getCollection()
            ->addOrder('report_id', 'DESC');
        if (count($report_collec) > 0) {
            $fist_item = $report_collec->getFirstItem();
            $previous_time = $fist_item->getData('created_time');
        } else $previous_time = '2014-01-01 01:01:01';

        $data = array();
        $data['discount_amount'] = 0;
        $data['order_count'] = 0;
        $data['voucher'] = 0;
        $data['voucher_orders'] = 0;

        if (Mage::helper('xpos/configXPOS')->getEnableTill() === '1') {
            $till_id = $this->getRequest()->getParam('till_id');
        } else {
            $till_id = NULL;
        }

        $collection = $this->_getOrderCollection($till_id, $previous_time);

        if (count($collection) > 0)
            foreach ($collection as $order) {
                $this->_arrCurrentOrders[] = $order->getData('entity_id');
                if (Mage::getEdition() == "Enterprise") {
                    if ((float)$order->getData('base_customer_balance_amount') || (float)$order->getData('base_gift_cards_amount')) {
                        $data['voucher_orders']++;
                    }
                    $data['voucher'] += floatval($order->getData('base_customer_balance_amount'));
                    $data['voucher'] += floatval($order->getData('base_gift_cards_amount'));
                }

                $data['discount_amount'] += floatval($order->getData('base_discount_amount'));
                $data['order_count']++;
            }

        Mage::getSingleton('adminhtml/session')->setDiscountInfo($data);
        return $data;
    }


    private $_arrCurrentOrders = array();

    public function getRefundTotal()
    {
        $report_collec = Mage::getModel('xpos/report')->getCollection()
            ->addOrder('report_id', 'DESC');
        if (count($report_collec) > 0) {
            $fist_item = $report_collec->getFirstItem();
            $previous_time = $fist_item->getData('created_time');
        } else $previous_time = '2014-01-01 01:01:01';

        $data = array();
        $data['refund_amount'] = 0;
        $data['order_count'] = 0;

        if (Mage::helper('xpos/configXPOS')->getEnableTill() === '1') {
            $till_id = $this->getRequest()->getParam('till_id');
        } else {
            $till_id = NULL;
        }

        $collection = $this->_getOrderCreditCollection($till_id, $previous_time);

        if (count($collection) > 0) {
            foreach ($collection as $order) {
                $data['refund_amount'] += floatval($order->getData('grand_total'));
                $data['order_count']++;
            }
        }
        return $data;
    }

    public function getRewardPointTotal()
    {
        $data = array();
        $collection = Mage::getModel('rewardpoints/rewardpointsorder')->getCollection()->addFieldToFilter('order_id', array('in' => $this->_arrCurrentOrders));
        foreach ($collection as $order) {
            $data['mwrp_amount'] += floatval($order->getData('money'));
            $data['order_count']++;
        }
        return $data;
    }

    public function getGiftCardTotal()
    {
        $data = array();
        $collection = Mage::getModel('giftcards/order')->getCollection()->addFieldToFilter('id_order', array('in' => $this->_arrCurrentOrders));
        foreach ($collection as $order) {
            $a = floatval($order->getData('discounted'));
            $data['gc_amount'] += $a;
            $data['order_count']++;
        }
        return $data;
    }

    private function _getReportCollection($till_id = 0)
    {
        $reportCollection = Mage::getModel('xpos/report')->getCollection()
            ->addFieldToFilter('till_id', array('eq' => $till_id))
            ->addOrder('report_id', 'DESC');
        return $reportCollection;

    }

    private function _getXPosTransacCollection($till_id = 0, $previous_time)
    {
        $xPosTransacCollection = Mage::getModel('xpos/transaction')->getCollection()
            ->addFieldToFilter('till_id', array('eq' => $till_id))
            ->addFieldToFilter('transac_flag', array('eq' => '1'))
            ->addFieldToFilter('created_time', array('from' => $previous_time))
            ->addOrder('transaction_id', 'DESC');
        return $xPosTransacCollection;

    }

    private function _getManualXPosTransacCollection($till_id = 0, $previous_time)
    {
        $xPosTransacCollection =
            Mage::getModel('xpos/transaction')->getCollection()
                ->addFieldToFilter('till_id', array('eq' => $till_id))
                ->addFieldToFilter('order_id', array('eq' => 'Manual'))
                ->addFieldToFilter('transac_flag', array('eq' => '1'))
                ->addFieldToFilter('created_time', array('from' => $previous_time))
                ->addOrder('transaction_id', 'ASC');
        return $xPosTransacCollection;

    }

    private function _getXPosOrderCollection($till_id = null, $previous_time)
    {
        $xPosOrderCollection =
            Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('created_at', array('from' => $previous_time));
        if (is_null($till_id)) {
            $xPosOrderCollection->addAttributeToFilter('till_id', array('null' => true));
        } else {
            $xPosOrderCollection->addAttributeToFilter('till_id', array('eq' => $till_id));
        }
        $xPosOrderCollection->load();
        return $xPosOrderCollection;

    }

    private function _getOrderCollection($till_id = null, $previous_time)
    {
        $xPosOrderCollection =
            Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('updated_at', array('from' => $previous_time));
        if (!is_null($till_id)) {
            $xPosOrderCollection->addAttributeToFilter('till_id', array('eq' => $till_id));
        }
        $xPosOrderCollection->load();
        return $xPosOrderCollection;
    }

    private function _getOrderCreditCollection($till_id = null, $previous_time)
    {
        $xPosOrderCollection =
            Mage::getModel('sales/order_creditmemo')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('updated_at', array('from' => $previous_time));
        if (!is_null($till_id)) {
            $xPosOrderCollection->addAttributeToFilter('till_id', array('eq' => $till_id));
        }
        $xPosOrderCollection->load();
        return $xPosOrderCollection;
    }
}
