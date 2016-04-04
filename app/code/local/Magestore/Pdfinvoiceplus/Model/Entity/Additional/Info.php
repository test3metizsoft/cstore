<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Additional_Info extends Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator {

    public function getStoreId() {
        return $this->getHelper()->getOrder()->getStoreId();
    }

    public function getHelper() {
        return Mage::helper('pdfinvoiceplus/pdf');
    }

    public function getTheOrderVariables() {
        $order = $this->getOrder(); // change by Jack 25/12
        $store = Mage::app()->getStore($this->getStoreId());
        foreach ($order->getData() as $key => $value) {
            $variables['order_' . $key] = array('value' => $value);
            if ($key == 'grand_total') {
                $variables['order_grand_total'] = array(
                    'value' => Mage::helper('core')->currency($order->getGrandTotal())
                );
            }
            if ($key == 'shipping_amount') {
                $variables['order_shipping_amount'] = array(
                    'value' => Mage::helper('core')->currency($order->getShippingAmount())
                );
            }
            if ($key == 'tax_amount') {
                $variables['order_tax_amount'] = array(
                    'value' => Mage::helper('core')->currency($order->getTaxAmount())
                );
            }
            if ($key == 'subtotal') {
                $variables['order_subtotal'] = array(
                    'value' => Mage::helper('core')->currency($order->getSubtotal())
                );
            }
            if ($key == 'created_at') {
                $variables['order_created_at'] = array(
                    'value' => Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', true)
                );
            }
        }
        return $variables;
        $variables = array(
            'order_number' => array(
                'value' => $order->getIncrementId(),
                'label' => Mage::helper('sales')->__('Order # %s')
            ),
            'purcase_from_website' => array(
                'value' => $store->getWebsite()->getName(),
                'label' => Mage::helper('sales')->__('Purchased From')
            ),
            'order_group' => array(
                'value' => $store->getGroup()->getName(),
                'label' => Mage::helper('pdfinvoiceplus')->__('Purchased From Store')
            ),
            'order_store' => array(
                'value' => $store->getName(),
                'label' => Mage::helper('sales')->__('Purchased From Website')
            ),
            'order_status' => array(
                'value' => $order->getStatus(),
                'label' => Mage::helper('sales')->__('Order Status')
            ),
            'order_date' => array(
                'value' => Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', false),
                'label' => Mage::helper('sales')->__('Order Date')
            ),
            'order_subtotal' => array(
                'value' => $order->formatPriceTxt($order->getSubtotal()),
                'label' => Mage::helper('sales')->__('Order Subtotal')
            ),
            'order_shippingtotal' => array(
                'value' => $order->formatPriceTxt($order->getShippingAmount()),
                'label' => Mage::helper('sales')->__('Shipping Total')
            ),
            'order_discountamount' => array(
                'value' => $order->formatPriceTxt($order->getDiscountAmount()),
                'label' => Mage::helper('sales')->__('Discount Amount')
            ),
            'order_taxamount' => array(
                'value' => $order->formatPriceTxt($order->getTaxAmount()),
                'label' => Mage::helper('sales')->__('Tax Amount')
            ),
            'order_grandtotal' => array(
                'value' => $order->formatPriceTxt($order->getGrandTotal()),
                'label' => Mage::helper('sales')->__('Grand Total')
            ),
            'order_totalpaid' => array(
                'value' => $order->formatPriceTxt($order->getTotalPaid()),
                'label' => Mage::helper('sales')->__('Total Paid')
            ),
            'order_totalrefunded' => array(
                'value' => $order->formatPriceTxt($order->getTotalRefunded()),
                'label' => Mage::helper('sales')->__('Total Refunded')
            ),
            'order_totaldue' => array(
                'value' => $order->formatPriceTxt($order->getTotalDue()),
                'label' => Mage::helper('sales')->__('Total Due')
            ),
        );

        return $variables;
    }

    public function getTheInvoiceVariables() {
        $invoice = $this->getInvoice(); // Change by Jack
         /* change by Zeus 04/12 */
        $variables[] = NULL;
        /* end change */
        if($invoice){  // Change by Jack 26/12
            $order = $invoice->getOrder();
            $ordered_items = $order->getAllItems();
            $total_state_tax = $total_county_tax = $total_city_tax = 0;
            foreach($ordered_items as $item){
                    $total_state_tax += $item->getStatetax();    
                    $total_county_tax += $item->getCountytax();    
                    $total_city_tax += $item->getCitytax();    
            }
            foreach ($invoice->getData() as $key => $value) {
                    $variables['invoice_' . $key] = array(
                        'value' => $value,
                    );

                    if($key == 'order_id'){
                        $variables['invoice_' . $key] = array(
                        'value' => $order->getIncrementId()
                    );
                    }
                    if ($key == 'grand_total') {
                        $variables['invoice_grand_total'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getGrandTotal())
                        );
                    }
                    if ($key == 'shipping_amount') {
                        $variables['invoice_shipping_amount'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getShippingAmount())
                        );
                    }
                    if ($key == 'tax_amount') {
                        $variables['invoice_tax_amount'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getTaxAmount())
                        );
                    }
                    if ($key == 'total_qty') {
                        $variables['invoice_total_qty'] = array(
                            'value' => (int)$invoice->getTotalQty()
                        );
                    }	
                    $variables['total_state_tax'] = array(
                            'value' => $order->formatPriceTxt($total_state_tax)
                    );
                    $variables['total_county_tax'] = array(
                            'value' => $order->formatPriceTxt($total_county_tax)
                    );
                    $variables['total_city_tax'] = array(
                            'value' => $order->formatPriceTxt($total_city_tax)
                    );
                    $variables['terms_condition'] = array(
                    'value' => Mage::getStoreConfig('xpos/receipt/term_and_condition')
                    );
                    if ($key == 'subtotal') {
                        $variables['invoice_subtotal'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getSubtotal())
                        );
                    }
                    $variables['total_taxes'] = array(
                    'value' => $order->formatPriceTxt($total_state_tax + $total_county_tax + $total_city_tax)
                    );
                    if ($key == 'created_at') {
                        $variables['invoice_created_at'] = array(
                            'value' => Mage::helper('core')->formatDate($invoice->getCreatedAt(), 'short', true)
                        );
                    }
                    if ($key == 'state') {
                        if ($value == 1) {
                            $variables['invoice_state'] = array(
                                'value' => Mage::helper('core')->__('Pendding')
                            );
                        } else if ($value == 2) {
                            $variables['invoice_state'] = array(
                                'value' => Mage::helper('core')->__('Paid')
                            );
                        } else {
                            $variables['invoice_state'] = array(
                                'value' => Mage::helper('core')->__('Closed')
                            );
                        }
                    }
                }
        }  // End Change
		
        return $variables;
    }

    public function getTheCreditmemoVariables() {
        $creditmemo = $this->getCreditmemo(); // Change By Jack
        /* change by Zeus 04/12 */
        $variables[] = NULL;
        /* end change */
        if($creditmemo){  // Change by Jack 26/12
                $order = $creditmemo->getOrder();
                foreach ($creditmemo->getData() as $key => $value) {
                    $variables['creditmemo_' . $key] = array('value' => $value);
                    if ($key == 'grand_total') {
                        $variables['creditmemo_grand_total'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getGrandTotal())
                        );
                    }
                    if($key == 'order_id'){
                        if($order->getIncrementId() != ''){
                        $variables['creditmemo_' . $key] = array(
                        'value' => $order->getIncrementId()
                    );
                    }
                    }
                /*End Change*/
                    if ($key == 'shipping_amount') {
                        $variables['creditmemo_shipping_amount'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getShippingAmount())
                        );
                    }
                    if ($key == 'tax_amount') {
                        $variables['creditmemo_tax_amount'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getTaxAmount())
                        );
                    }
                    if ($key == 'subtotal') {
                        $variables['creditmemo_subtotal'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getSubtotal())
                        );
                    }
                    if ($key == 'created_at') {
                        $variables['creditmemo_created_at'] = array(
                            'value' => Mage::helper('core')->formatDate($creditmemo->getCreatedAt(), 'short', true)
                        );
                    }
                    if ($key == 'state') {
                        if ($value == 1) {
                            $variables['creditmemo_state'] = array(
                                'value' => Mage::helper('core')->__('Pendding')
                            );
                        } else if ($value == 2) {
                            $variables['creditmemo_state'] = array(
                                'value' => Mage::helper('core')->__('Paid')
                            );
                        } else {
                            $variables['creditmemo_state'] = array(
                                'value' => Mage::helper('core')->__('Closed')
                            );
                        }
                    }
                }
        } // End Change
        return $variables;
    }

    public function getTheCustomerVariables() {
        $order = $this->getOrder(); // change by Jack 25/12
        $store = Mage::app()->getStore($this->getStoreId());
        $customerId = $order->getCustomerId();
        $getCustomer = Mage::getModel('customer/customer')->load($customerId);
        $getCustomerGroup = Mage::getModel('customer/group')->load((int) $order->getCustomerGroupId())->getCode();
        $variables = array();
        foreach ($getCustomer->getData()  as $key =>$value){
            $variables['customer_'.$key] = array('value' => $value);
            if($key =='created_at'){
                $variables['customer_created_at']=array(
                    'value' =>Mage::helper('core')->formatDate($getCustomer->getData('created_at'), 'short', true)
                );
            }
             if($key =='created_in'){
                $variables['customer_created_in']=array(
                    'value' =>$getCustomer->getData('created_in')
                );
            }
            if($key =='dbo'){
                $variables['customer_created_at']=array(
                    'value' =>Mage::helper('core')->formatDate($getCustomer->getData('dob'), 'short', true)
                );
            }
        }
        return $variables;
    }

    public function getThePaymentInfo($type) {
        $order = $this->getOrder(); // change by Jack 25/12
        $paymentInfo = $order->getPayment()->getMethodInstance()->getTitle();

        $variables = array(
            $type.'_payment_method' => array(
                'value' => $paymentInfo,
                'label' => Mage::helper('sales')->__('Billing Method'),
            ),
            $type.'_billing_method_currency' => array(
                'value' => $order->getOrderCurrencyCode(),
                'label' => Mage::helper('sales')->__('Order was placed using'),
            ),
        );
        return $variables;
    }

    public function getTheShippingInfo($type) {
        $order = $this->getOrder(); // change by Jack 25/12
        if ($order->getShippingDescription()) {
            $shippingInfo = $order->getShippingDescription();
        } else {
            $shippingInfo = '';
        }

        $variables = array(
            $type.'_shipping_method' => array(
                'value' => $shippingInfo,
                'label' => Mage::helper('sales')->__('Shipping Information'),
            ),
        );
        return $variables;
    }

    public function getTheAddresInfo($type) {
        $order = $this->getOrder();  // change by Jack 25/12
        $billingInfo = $order->getBillingAddress()->getFormated(true);
        if ($order->getShippingAddress()) {
            $shippingInfo = $order->getShippingAddress()->getFormated(true);
        } else {
            $shippingInfo = '';
        }
        $variables = array(
            $type.'_billing_address' => array(
                'value' => $billingInfo,
                'label' => Mage::helper('sales')->__('Billing Address'),
            ),
            $type.'_shipping_address' => array(
                'value' => $shippingInfo,
                'label' => Mage::helper('sales')->__('Shipping Address'),
            )
        );
        return $variables;
    }
    /* change by Jack 27/12 */
    public function detectType(){
        if(Mage::getSingleton('core/session')->getType() == 'invoice')
            return 'invoice';
        else if(Mage::getSingleton('core/session')->getType() == 'creditmemo')
            return 'creditmemo';
        else
            return 'order';
    }
    public function getTheInfoMergedVariables() {
        $type = $this->detectType();
        if ($type == 'invoice') {
			$vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('invoice')
                    , $this->getThePaymentInfo('invoice')
                    , $this->getTheShippingInfo('invoice')                    
                    , $this->getTheInvoiceVariables()
            );
        } else if ($type == 'creditmemo') {
			$vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('creditmemo')
                    , $this->getThePaymentInfo('creditmemo')
                    , $this->getTheShippingInfo('creditmemo')
                    , $this->getTheCreditmemoVariables()
            );
        } else {
            $vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('order')
                    , $this->getThePaymentInfo('order')
                    , $this->getTheShippingInfo('order')
            );
        }
        $processedVars = Mage::helper('pdfinvoiceplus')->arrayToStandard($vars);
        return $processedVars;
    }
    // end change
}