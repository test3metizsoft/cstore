<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Pdfinvoiceplus Helper
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Helper_Variable extends Mage_Core_Helper_Abstract
{
    const _PRE_VAR_ORDER = 'order';
    const _PRE_VAR_INVOICE = 'invoice';
    const _PRE_VAR_CREDITMEMO = 'creditmemo';
    const PRE_VAR_ITEMS = 'items';
    
    protected $_helper;
    protected $_model;
    protected $_type;


    public function __construct() {
        $this->_helper = Mage::helper('pdfinvoiceplus/pdf');
    }
    
    /**
     * set Order|Invoice|Creditmemo model
     * @param type $model
     */
    public function setModel($model){
        $this->_model = $model;
        return $this;
    }
    
    public function setType($type){
        $this->_type = $type;
        return $this;
    }

    public function getCustomerVariables()
    {
        $customer =$this->getCustomerVars();
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Customer'),
            'value' => $customer
        );
        return $variables;
    }
        
//    public function getOrderVariables(){
//        
//        return $this->getOrderVars();
//    }
    //    public function getInvoiceVariables(){
//        
//        return $this->getInvoiceVars();
//    }
    //    public function getCreditmemoVariables(){
//        
//        return $this->getCreditmemoVars();
//    }
    
    public function getInvoiceVariables()
    {
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Invoice'),
            'value' => $this->getInvoiceVars()
        );
        return $variables;
    }
    
    public function getCreditmemoVariables()
    {
        $creditmemo =$this->getCreditmemoVars();
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Creditmemo'),
            'value' => $creditmemo
        );
        return $variables;
    }
    
    public function getShipPayVariables()
    {
        $variables[] = array(
            'value' => '{{var billing_method}}',
            'label' => Mage::helper('sales')->__('Billing Method'),
        );
        $variables[] = array(
            'value' => '{{var billing_method_currency}}',
            'label' => Mage::helper('sales')->__('Order was placed using'),
        );
        $variables[] = array(
            'value' => '{{var shipping_method}}',
            'label' => Mage::helper('sales')->__('Shipping Information'),
        );

        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Shipping and Billing'),
            'value' => $variables
        );
        return $variables;
    }
    
    public function getHelper() {
        return Mage::helper('pdfinvoiceplus/pdf');
    }
    
    public function getOrderVariables(){
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Order'),
            'value' => $this->getOrderVars()
        );
        return $variables;
    }
    
    /***********************/
    /*
     * get variables
     */
    public function getCustomerVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Customer();
    }
    
    public function getOrderVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Order();
    }
    
    public function getOrderItemVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Order_Items();
    }
    
    public function getInvoiceVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Invoice();
    }
    
    public function getInvoiceItemVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Invoice_Items();
    }
    
    public function getCreditmemoVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Creditmemo();
    }
    
    public function getCreditmemoItemVars(){
        return Mage::getModel('pdfinvoiceplus/variables')->getVarsConfig_Creditmemo_Items();
    }
    
    
    
//    public function getAdditionalOrderVars(){
//        return $this->_additional_order_vars;
//    }
//    
//    public function getAdditionalInvoiceVars(){
//        return $this->_additional_invoice_vars;
//    }
//    
//    public function getAdditionalCreditmemoVars(){
//        return $this->_additional_creditmemo_vars;
//    }
    
    /**
     * 
     * @param array(array('value'=>'var_name','label'=>'string')) $vars
     * @param string $prefix
     * @return array()
     */
    protected function bindPrefix($vars = array(), $prefix = ''){
        $arr = array();
        foreach ($vars as $var){
            $arr[] = array(
                'value' =>  $prefix.'_'.$var['value'],
                'label' =>  $var['label']
            );
        }
        return $arr;
    }

    /**
     * 
     * @param array('var') $vars
     * @param string $prefix
     */
    protected function unbindPrefix($vars = array(), $prefix = ''){
        $arr = array();
        foreach ($vars as $var){
            $arr[] = str_replace($prefix.'_', '',$var);
        }
        return $arr;
    }
    
    /**
     * 
     * @param type $address Mage_Sales_Model_Order_Address
     * @return string
     */
    protected function getAddressOption($address){
        if(is_object($address)){
            return $address->getFormated(true);
        }
        return '';
    }
    
    protected function verifyVars($newvars, $oldvars){
        $new = array();
        foreach ($oldvars as $var){
            if(!isset($newvars[$var])){
                $new[$var] = null;
            }else{
                $new[$var] = $newvars[$var];
            }
        }
        return $new;
    }
    
    protected function getOrder() {
        if(is_object($this->_model) && $this->_model instanceof Mage_Sales_Model_Order ){
            return $this->_model;
        }
        if (Mage::registry('current_order'))
            return Mage::registry('current_order');
        elseif ($this->getInvoice()->getId()) {
            $order = $this->getInvoice()->getOrder();
        } elseif ($this->getCreditmemo()->getId()) {
            $order = $this->getCreditmemo()->getOrder();
        } else {
            $orderId = Mage::app()->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->load($orderId);
        }
        if ($order->getId())
            Mage::register('current_order', $order);
        return $order;
    }
    
    protected function getInvoice() {
        if(is_object($this->_model) && $this->_model instanceof Mage_Sales_Model_Order_Invoice ){
            return $this->_model;
        }
        if (Mage::registry('current_invoice'))
            return Mage::registry('current_invoice');
        else {
            $invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            return $invoice;
        }
    }

    protected function getCreditmemo() {
        if(is_object($this->_model) && $this->_model instanceof Mage_Sales_Model_Order_Creditmemo ){
            return $this->_model;
        }
        if (Mage::registry('current_creditmemo'))
            return Mage::registry('current_creditmemo');
        else {
            $creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id');
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
            return $creditmemo;
        }
    }

    
    public function toArrayKeyValue($arr, $key_index = 'value', $value_index = 'label'){
        if(!is_null($key_index) && !is_null($value_index)){
            if(is_array($arr)){
                $array_key = array();
                foreach($arr as $label_value){
                    if(is_array($label_value)){
                        $array_key[$label_value[$key_index]] = $label_value[$value_index];
                    }else{
                        $array_key[$label_value] = $label_value; 
                    }
                }
                return $array_key;
            }
        }
        return array();
    }
    
    
    
    /*************************************************************************/
    /**
     * map all data for variable
     * return mixed
     */
    public function getOrderData($vars=null){
        
        $order = $this->getOrder();
        $var_model = Mage::getModel('pdfinvoiceplus/variables');
        
        if(is_null($vars)){
            $vars = array_keys($order->getData());
        }
        $data = array();
        if(!is_object($order)){
            return;
        }
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $uvars = $this->unbindPrefix($vars, self::_PRE_VAR_ORDER);
        foreach($uvars as $var){
            if(in_array($var, array_keys($var_model->_additional_order_vars))){
                switch ($var){
                    case 'payment_method':
                        $data[] = array(
                            'value'=>$var,
                            'label'=>($payment = $order->getPayment())?$payment->getMethodInstance()->getTitle():null
                        );
                        break;
                    case 'shipping_method':
                        if ( $order->getShippingDescription() ){
                           $data[] = array(
                                'value'=>$var,
                                'label'=>$order->getShippingDescription()
                            );
                        }
                        break;
                    case 'currency':
                        $data[] = array(
                            'value'=>$var,
                            'label'=>$order->getOrderCurrencyCode()
                        );
                        break;
                    case 'billing_address':
                        if($order->getBillingAddress()){
                            $address = $this->getAddressOption($order->getBillingAddress());
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$address
                            );
                        }
                        break;
                    case 'shipping_address':
                        if($order->getShippingAddress()){
                            $address = $this->getAddressOption($order->getShippingAddress());
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$address
                            );
                        }
                        break;
                    default:
                        break;
                }
            }else{//if auto
                $data[] = array(
                    'value'=>$var,
                    'label'=>$order->getData($var)
                );
            }
        }
        return $this->toArrayKeyValue($this->bindPrefix($data, self::_PRE_VAR_ORDER));
        //return $this->verifyVars($this->toArrayKeyValue($this->bindPrefix($data, self::_PRE_VAR_ORDER)),$vars);
    }

    public function getInvoiceData($vars){
        $invoice = $this->getInvoice();
        $var_model = Mage::getModel('pdfinvoiceplus/variables');
        $data = array();
        if(!is_object($invoice)){
            return;
        }
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $uvars = $this->unbindPrefix($vars, self::_PRE_VAR_INVOICE);
        foreach($uvars as $var){
            if(in_array($var, array_keys($var_model->_additional_invoice_vars))){
                switch ($var){
                    case 'payment_method':
                        $data[] = array(
                            'value'=>$var,
                            'label'=>($payment = $invoice->getOrder()->getPayment())?$payment->getMethodInstance()->getTitle():null
                        );
                        break;
                    case 'shipping_method':
                        if ( $invoice->getOrder()->getShippingDescription() ){
                           $data[] = array(
                                'value'=>$var,
                                'label'=>$invoice->getOrder()->getShippingDescription()
                            );
                        }
                        break;
                    case 'currency':
                        $data[] = array(
                            'value'=>$var,
                            'label'=>$invoice->getOrderCurrencyCode()
                        );
                        break;
                    case 'billing_address':
                        if($invoice->getBillingAddress()){
                            $address = $this->getAddressOption($invoice->getBillingAddress());
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$address
                            );
                        }
                        break;
                    case 'shipping_address':
                        if($invoice->getShippingAddress()){
                            $address = $this->getAddressOption($invoice->getShippingAddress());
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$address
                            );
                        }
                        break;
                    default:
                        break;
                }
            }else{//if auto
                $data[] = array(
                    'value'=>$var,
                    'label'=>$invoice->getData($var)
                );
            }
        }
        
        return $this->verifyVars($this->toArrayKeyValue($this->bindPrefix($data, self::_PRE_VAR_INVOICE)),$vars);
    }
    
    public function getCreditmemoData($vars){
        $creditmemo = $this->getCreditmemo();
        $var_model = Mage::getModel('pdfinvoiceplus/variables');
        $data = array();
        if(!is_object($creditmemo)){
            return;
        }
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $uvars = $this->unbindPrefix($vars, self::_PRE_VAR_CREDITMEMO);
        foreach($uvars as $var){
            if(in_array($var, array_keys($var_model->_additional_creditmemo_vars))){
                switch ($var){
                    case 'payment_method':
                        $data[] = array(
                            'value'=>$var,
                            'label'=>($payment = $creditmemo->getOrder()->getPayment())?$payment->getMethodInstance()->getTitle():null
                        );
                        break;
                    case 'shipping_method':
                        if ( $creditmemo->getOrder()->getShippingDescription() ){
                           $data[] = array(
                                'value'=>$var,
                                'label'=>$creditmemo->getOrder()->getShippingDescription()
                            );
                        }
                        break;
                    case 'currency':
                        $data[] = array(
                            'value'=>$var,
                            'label'=>$creditmemo->getOrderCurrencyCode()
                        );
                        break;
                    case 'billing_address':
                        if($creditmemo->getBillingAddress()){
                            $address = $this->getAddressOption($creditmemo->getBillingAddress());
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$address
                            );
                        }
                        break;
                    case 'shipping_address':
                        if($creditmemo->getShippingAddress()){
                            $address = $this->getAddressOption($creditmemo->getShippingAddress());
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$address
                            );
                        }
                        break;
                    default:
                        break;
                }
            }else{//if auto
                $data[] = array(
                    'value'=>$var,
                    'label'=>$creditmemo->getData($var)
                );
            }
        }
        
        return $this->verifyVars($this->toArrayKeyValue($this->bindPrefix($data, self::_PRE_VAR_CREDITMEMO)),$vars);
    }
    
    public function getOrderItemsData($vars){
        $order = $this->getOrder();
        $var_model = Mage::getModel('pdfinvoiceplus/variables');
        $data_rows = array();
        if(!is_object($order)){
            return $data_rows;
        }
        $items = $order->getItemsCollection();
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $vars = $this->unbindPrefix($vars, self::PRE_VAR_ITEMS);
        foreach ($items as $item){
            $data = array();
            foreach ($vars as $var){
                if(in_array($var, array_keys($var_model->_additional_order_items_vars))){
                    switch ($var){
                        case 'small_image':
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$item->getProduct()->getData('small_image')
                            );
                            break;
                    }
                }else{
                    $data[] = array(
                        'value'=>$var,
                        'label'=>$item->getData($var)
                    );
                }
            }
            $data_rows[] = $this->bindPrefix($data, self::PRE_VAR_ITEMS);
        }
        return $data_rows;
    }

    public function getInvoiceItemsData($vars){
        $invoice = $this->getInvoice();
        $var_model = Mage::getModel('pdfinvoiceplus/variables');
        $data_rows = array();
        if(!is_object($invoice)){
            return $data_rows;
        }
        $items = $invoice->getItemsCollection();
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $vars = $this->unbindPrefix($vars, self::PRE_VAR_ITEMS);
        foreach ($items as $item){
            $data = array();
            foreach ($vars as $var){
                if(in_array($var, array_keys($var_model->_additional_invoice_items_vars))){
                    switch ($var){
                        case 'small_image':
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$item->getProduct()->getData('small_image')
                            );
                            break;
                    }
                }else{
                    $data[] = array(
                        'value'=>$var,
                        'label'=>$item->getData($var)
                    );
                }
            }
            $data_rows[] = $this->bindPrefix($data, self::PRE_VAR_ITEMS);
        }
        return $data_rows;
    }
    
    public function getCreditmemoItemsData($vars){
        $creditmemo = $this->getCreditmemo();
        $var_model = Mage::getModel('pdfinvoiceplus/variables');
        $data_rows = array();
        if(!is_object($creditmemo)){
            return $data_rows;
        }
        $items = $creditmemo->getItemsCollection();
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $vars = $this->unbindPrefix($vars, self::PRE_VAR_ITEMS);
        foreach ($items as $item){
            $data = array();
            foreach ($vars as $var){
                if(in_array($var, array_keys($var_model->_additional_creditmemo_items_vars))){
                    switch ($var){
                        case 'small_image':
                            $data[] = array(
                                'value'=>$var,
                                'label'=>$item->getProduct()->getData('small_image')
                            );
                            break;
                    }
                }else{
                    $data[] = array(
                        'value'=>$var,
                        'label'=>$item->getData($var)
                    );
                }
            }
            $data_rows[] = $this->bindPrefix($data, self::PRE_VAR_ITEMS);
        }
        return $data_rows;
    }

    public function getCustomerData($vars, $type){
        if(!is_array($vars)){ //if return array
            $vars = array($vars);
        }
        $vars = $this->unbindPrefix($vars, 'customer');
        $data = array();
        if($type == 'order'){
            $order = $this->getOrder();
            if(!is_object($order)){
                return $data;
            }
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            foreach($vars as $var){
                $data[] = array(
                    'value'=>$var,
                    'label'=>$customer->getData($var)
                );
            }
        }else if($type == 'invoice'){
            $invoice = $this->getInvoice();
            if(!is_object($invoice)){
                return $data;
            }
            $customer = Mage::getModel('customer/customer')->load($invoice->getOrder()->getCustomerId());
            foreach($vars as $var){
                $data[] = array(
                    'value'=>$var,
                    'label'=>$customer->getData($var)
                );
            }
        }else if($type == 'creditmemo'){
            $creditmemo = $this->getCreditmemo();
            if(!is_object($creditmemo)){
                return $data;
            }
            $customer = Mage::getModel('customer/customer')->load($creditmemo->getOrder()->getCustomerId());
            foreach($vars as $var){
                $data[] = array(
                    'value'=>$var,
                    'label'=>$customer->getData($var)
                );
            }
        }
        return $this->toArrayKeyValue($this->bindPrefix($data, 'customer'));
    }
    
    // variable order Barcode
     public function getOrderVariablesBarcode()
    {
        $variables[] = array(
            'value' => '{{var order_status}}',
            'label' => Mage::helper('tax')->__('Status'),
        );
        $variables[] = array(
            'value' => '{{var order_increment_id}}',
            'label' => Mage::helper('sales')->__('Increment Id')
        );
        $variables[] = array(
            'value' => '{{var order_created_at}}',
            'label' => Mage::helper('sales')->__('Invoice Date')
        );
        $variables[] = array(
            'value' => '{{var order_total_qty}}',
            'label' => Mage::helper('sales')->__('Qty')
        );
        $variables[] = array(
            'value' => '{{var order_billing_address}}',
            'label' => Mage::helper('sales')->__('Billing Address')
        );
        $variables[] = array(
            'value' => '{{var order_shipping_address}}',
            'label' => Mage::helper('sales')->__('Shipping Address')
        );
        $variables[] = array(
            'value' => '{{var order_billing_method}}',
            'label' => Mage::helper('sales')->__('Billing Method')
        );
        $variables[] = array(
            'value' => '{{var order_shipping_method}}',
            'label' => Mage::helper('sales')->__('Shipping Method')
        );
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Order'),
            'value' => $variables
        );
        return $variables;
    }
    // Variable invoice Barcode
        public function getInvoiceVariablesBarcode()
    {
        $variables[] = array(
            'value' => '{{var invoice_state}}',
            'label' => Mage::helper('tax')->__('State'),
        );
        $variables[] = array(
            'value' => '{{var invoice_increment_id}}',
            'label' => Mage::helper('sales')->__('Increment Id')
        );
        $variables[] = array(
            'value' => '{{var invoice_created_at}}',
            'label' => Mage::helper('sales')->__('Invoice Date')
        );
        $variables[] = array(
            'value' => '{{var invoice_order_id}}',
            'label' => Mage::helper('sales')->__('Order Id')
        );
        $variables[] = array(
            'value' => '{{var invoice_total_qty}}',
            'label' => Mage::helper('sales')->__('Qty')
        );
        $variables[] = array(
            'value' => '{{var invoice_billing_address}}',
            'label' => Mage::helper('sales')->__('Billing Address')
        );
        $variables[] = array(
            'value' => '{{var invoice_shipping_address}}',
            'label' => Mage::helper('sales')->__('Shipping Address')
        );
        $variables[] = array(
            'value' => '{{var invoice_billing_method}}',
            'label' => Mage::helper('sales')->__('Billing Method')
        );
        $variables[] = array(
            'value' => '{{var invoice_shipping_method}}',
            'label' => Mage::helper('sales')->__('Shipping Method')
        );
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Invoice'),
            'value' => $variables
        );
        return $variables;
    }
    
       public function getCreditmemoVariablesBarcode()
    {
        $variables[] = array(
            'value' => '{{var creditmemo_state}}',
            'label' => Mage::helper('tax')->__('State'),
        );
        $variables[] = array(
            'value' => '{{var creditmemo_increment_id}}',
            'label' => Mage::helper('sales')->__('Increment Id')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_created_at}}',
            'label' => Mage::helper('sales')->__('Invoice Date')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_order_id}}',
            'label' => Mage::helper('sales')->__('Order Id')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_total_qty}}',
            'label' => Mage::helper('sales')->__('Qty')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_billing_address}}',
            'label' => Mage::helper('sales')->__('Billing Address')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_shipping_address}}',
            'label' => Mage::helper('sales')->__('Shipping Address')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_billing_method}}',
            'label' => Mage::helper('sales')->__('Billing Method')
        );
        $variables[] = array(
            'value' => '{{var creditmemo_shipping_method}}',
            'label' => Mage::helper('sales')->__('Shipping Method')
        );
        $variables = array(
            'label' => Mage::helper('pdfinvoiceplus')->__('Creditmemo'),
            'value' => $variables
        );
        return $variables;
    }
    
}
