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
 * Pdfinvoiceplus Variables Model
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Model_Variables extends Varien_Object {

    const PATH_CONFIG_CUSTOMER = 'pdfinvoiceplus/variables/customer';
    const PATH_CONFIG_ORDER = 'pdfinvoiceplus/variables/order';
    const PATH_CONFIG_ORDER_ITEMS = 'pdfinvoiceplus/variables/order_items';
    const PATH_CONFIG_INVOICE = 'pdfinvoiceplus/variables/invoice';
    const PATH_CONFIG_INVOICE_ITEMS = 'pdfinvoiceplus/variables/invoice_items';
    const PATH_CONFIG_CREDITMEMO = 'pdfinvoiceplus/variables/creditmemo';
    const PATH_CONFIG_CREDITMEMO_ITEMS = 'pdfinvoiceplus/variables/creditmemo_items';
    const PRE_CUSTOMER = 'customer';
    const PRE_VAR_ORDER = 'order';
    const PRE_VAR_INVOICE = 'invoice';
    const PRE_VAR_CREDITMEMO = 'creditmemo';
    const PRE_VAR_ITEMS = 'items';

    public $_additional_order_vars;
    public $_additional_invoice_vars;
    public $_additional_creditmemo_vars;
    public $_additional_order_items_vars;
    public $_additional_invoice_items_vars;
    public $_additional_creditmemo_items_vars;
    protected $_helper;
    public $main_var_customer = array
        (
        "email",
        "firstname"
    );
    public $main_var_order = array
        (
        "increment_id",
        "status",
        "created_at",
        "discount_amount",
        "tax_amount",
        "grand_total",
        "total_paid",
        "total_qty_ordered",
        "total_due",
        "billing_address",
        "shipping_address",
        "payment_method",
        "shipping_method"
    );
    public $main_var_invoice = array
        (
        "increment_id",
        "state",
        "created_at",
        "grand_total",
        "total_paid",
        "billing_address",
        "shipping_address",
        "payment_method",
        "shipping_method"
    );
    public $main_var_creditmemo = array
        (
        "increment_id",
        "state",
        "created_at",
        "grand_total",
        "billing_address",
        "shipping_address",
        "payment_method",
        "shipping_method",
    );
    public $main_var_order_item = array
        (
        "sku",
        "name",
        "small_image",
        "discount_amount",
        "row_total",
        "row_total_incl_tax",
        "qty_ordered",
        "qty_invoiced",
        "qty_refunded"
    );
    public $main_var_invoice_item = array
        (
        "sku",
        "name",
        "small_image",
        "discount_amount",
        "row_total",
        "row_total_incl_tax",
        "qty",
    );
    public $main_var_creditmemo_item = array
        (
        "sku",
        "name",
        "small_image",
        "discount_amount",
        "row_total",
        "row_total_incl_tax",
        "qty",
    );

    /* Hiden variables */
    public $hiden_vars_customer = array
        (
        "confirmation",
        "default_billing",
        "default_shipping",
        "disable_auto_group_change",
        "gender",
        "group_id",
        "password_hash",
        "prefix",
        "rp_token",
        "rp_token_created_at",
        "store_id",
        "suffix",
        "website_id"
    );
    public $hiden_vars_order = array(
        "entity_id",
        "state",
        "coupon_code",
        "protect_code",
        "is_virtual",
        "store_id",
        "customer_id",
        "discount_canceled",
        "discount_refunded",
        "shipping_canceled",
        "shipping_refunded",
        "shipping_tax_amount",
        "shipping_tax_refunded",
        "store_to_order_rate",
        "subtotal_canceled",
        "subtotal_invoiced",
        "subtotal_refunded",
        "tax_canceled",
        "tax_invoiced",
        "tax_refunded",
        "total_canceled",
        "total_offline_refunded",
        "total_online_refunded",
        "total_refunded",
        "can_ship_partially",
        "can_ship_partially_item",
        "customer_is_guest",
        "billing_address_id",
        "customer_group_id",
        "edit_increment",
        "forced_shipment_with_invoice",
        "gift_message_id",
        "payment_auth_expiration",
        "paypal_ipn_customer_notified",
        "quote_address_id", "quote_id",
        "shipping_address_id",
        "adjustment_negative",
        "adjustment_positive",
        "shipping_discount_amount",
        "customer_dob",
        "applied_rule_ids",
        "customer_email",
        "customer_firstname",
        "customer_lastname",
        "customer_middlename",
        "customer_prefix",
        "customer_suffix",
        "customer_taxvat",
        "ext_customer_id",
        "ext_order_id",
        "global_currency_code",
        "hold_before_state",
        "hold_before_status",
        "order_currency_code",
        "original_increment_id",
        "relation_child_id",
        "relation_child_real_id",
        "relation_parent_id",
        "relation_parent_real_id",
        "remote_ip",
        "shipping_method",
        "store_currency_code",
        "x_forwarded_for",
        "currency_code",
        "currency_rate",
        "custbalance_amount",
        "is_hold",
        "is_multi_payment",
        "real_order_id",
        "tax_percent",
        "hidden_tax_amount",
        "shipping_hidden_tax_amount",
        "hidden_tax_invoiced",
        "hidden_tax_refunded",
        "shipping_incl_tax"
    );
    public $hiden_vars_order_items = array(
        "item_id",
        "order_id",
        "parent_item_id",
        "quote_item_id",
        "store_id",
        "created_at",
        "updated_at",
        "product_id",
//            "product_type",
//            "product_options",
        "is_virtual",
        "applied_rule_ids",
        "additional_data",
        "free_shipping",
        "is_qty_decimal",
        "no_discount",
        "qty_backordered",
//            "qty_canceled",
//            "qty_refunded",
//            "qty_shipped",
//            "tax_percent",
//            "tax_amount",
//            "tax_invoiced",
//            "discount_percent",
//            "discount_invoiced",
//            "amount_refunded",
        "row_invoiced",
        "row_weight",
        "gift_message_id",
        "gift_message_available",
        "tax_before_discount",
        "weee_tax_applied",
        "weee_tax_applied_amount",
        "weee_tax_applied_row_amount",
        "weee_tax_disposition",
        "weee_tax_row_disposition",
        "ext_order_item_id",
        "locked_do_invoice",
        "locked_do_ship",
//            "price_incl_tax",
//            "row_total_incl_tax",
        "hidden_tax_amount",
        "hidden_tax_invoiced",
        "hidden_tax_refunded",
        "is_nominal",
        "tax_canceled",
        "hidden_tax_canceled",
        "tax_refunded",
        "discount_refunded"
    );
    public $hiden_vars_invoice = array(
        "entity_id",
        "store_id",
        "shipping_tax_amount",
        "store_to_order_rate",
        "shipping_amount",
        "billing_address_id",
        "is_used_for_refund",
        "order_id",
        "can_void_flag",
        "shipping_address_id",
        "cybersource_token",
        "store_currency_code",
        "transaction_id",
        "order_currency_code",
        "global_currency_code",
        "customer_id",
        "invoice_status_id",
        "invoice_type",
        "is_virtual",
        "real_order_id",
        "hidden_tax_amount",
        "shipping_hidden_tax_amount",
        "shipping_incl_tax"
    );
    public $hiden_vars_invoice_items = array(
        "entity_id",
        "parent_id",
        "weee_tax_applied_row_amount",
        "weee_tax_row_disposition",
        "weee_tax_applied_amount",
        "weee_tax_disposition",
        "product_id",
        "order_item_id",
        "additional_data",
        "weee_tax_applied",
        "shipment_id",
        "hidden_tax_amount",
        "invoice_status_id"
    );
    public $hiden_vars_creditmemo = array(
        "entity_id",
        "store_id",
        "adjustment_positive",
        "store_to_order_rate",
        "adjustment_negative",
        "adjustment",
        "shipping_tax_amount",
        "order_id",
//            "state",
        "shipping_address_id",
        "billing_address_id",
        "invoice_id",
        "cybersource_token",
        "store_currency_code",
        "order_currency_code",
        "global_currency_code",
        "transaction_id",
        "increment_id",
        "hidden_tax_amount",
        "shipping_hidden_tax_amount",
        "shipping_incl_tax",
        "creditmemo_status"
    );
    public $hiden_vars_creditmemo_items = array(
        "entity_id",
        "parent_id",
        "weee_tax_row_disposition",
        "weee_tax_applied_amount",
        "weee_tax_disposition",
        "product_id",
        "order_item_id",
        "additional_data",
        "weee_tax_applied",
        "hidden_tax_amount"
    );

    public function __construct() {
        $this->_helper = Mage::helper('pdfinvoiceplus/variable');
        $this->initAdditionalOrderVars()
            ->initAdditionalInvoiceVars()
            ->initAdditionalCreditmemoVars()
            ->initAdditionalOrderItemsVars()
            ->initAdditionalInvoiceItemsVars()
            ->initAdditionalCreditmemoItemsVars();
    }

    /* get all available variables from db */

     public function getAllVars_Customer() {
        /* change by Jack 01/12 */
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
		$customer_eav_attribute = $resource->getTableName('customer_eav_attribute');
		$eav_attribute = $resource->getTableName('eav_attribute');
		$eav_entity_type = $resource->getTableName('eav_entity_type');
        $variableList = $conn->fetchAll("select e.attribute_code, e.frontend_label "
            . "from $customer_eav_attribute as c left join $eav_attribute as e "
            . "on e.attribute_id = c.attribute_id "
            . "where e.entity_type_id = (select entity_type_id from $eav_entity_type where entity_type_code = 'customer')");
        /* end change */
		//to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['frontend_label'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_CUSTOMER . "_{$var['attribute_code']}}}",
                    'label' => Mage::helper('customer')->__(ucwords(str_replace('_', ' ', $var['attribute_code'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_CUSTOMER . "_{$var['attribute_code']}}}",
                    'label' => Mage::helper('customer')->__("{$var['frontend_label']}")
                );
            }
        }
        return $variables;
    }

    public function getVarsOnHiden_Customer() {
        $variableList = $this->getAllVars_Customer();
        $variables = array();
        $varBlackList = $this->getHidenVarCustomer();
        /* Change by Zeus 03/12 */
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        /* End change */
        return $variables;
    }
    
    public function getVarsConfig_Customer() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_CUSTOMER);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Customer());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Customer();
        }
    }

    public function getAllVars_Order() {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        $table_name = $resource->getTableName('sales/order');
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='" . $table_name . "' "
            . "AND COLUMN_NAME not like '%base_%' "
            . "AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['COLUMN_COMMENT'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ORDER . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $var['COLUMN_NAME'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ORDER . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__("{$var['COLUMN_COMMENT']}")
                );
            }
        }
        //add additional vars
        foreach ($this->_additional_order_vars as $key => $var) {
            $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_ORDER . "_{$key}}}",
                'label' => Mage::helper('sales')->__($var)
            );
        }

        return $variables;
    }

    public function getVarsOnHiden_Order() {
        $variableList = $this->getAllVars_Order();
        $variables = array();
        $varBlackList = $this->getHidenVarOrder();
        /* Change by Zeus 03/12 */
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        /* End change */
        return $variables;
    }
    
    public function getVarsConfig_Order() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_ORDER);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Order());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Order();
        }
    }

    public function getAllVars_Order_Items() {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        $table_name = $resource->getTableName('sales/order_item');
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='" . $table_name . "' "
            . "AND COLUMN_NAME not like '%base_%' "
            . "AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['COLUMN_COMMENT'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $var['COLUMN_NAME'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__("{$var['COLUMN_COMMENT']}")
                );
            }
        }
        //add additional vars
        foreach ($this->_additional_order_items_vars as $key => $var) {
            $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$key}}}",
                'label' => Mage::helper('sales')->__($var)
            );
        }
        return $variables;
    }

    public function getVarsOnHiden_Order_Items() {
        $variableList = $this->getAllVars_Order_Items();
        $variables = array();
        $varBlackList = $this->getHidenVarOrderItem();
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        return $variables;
    }
    
    public function getVarsConfig_Order_Items() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_ORDER_ITEMS);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Order_Items());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Order_Items();
        }
    }

    public function getAllVars_Invoice() {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        $table_name = $resource->getTableName('sales/invoice');
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='" . $table_name . "' "
            . "AND COLUMN_NAME not like '%base_%' "
            . "AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['COLUMN_COMMENT'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_INVOICE . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $var['COLUMN_NAME'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_INVOICE . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__("{$var['COLUMN_COMMENT']}")
                );
            }
        }
        //add additional vars
        foreach ($this->_additional_invoice_vars as $key => $var) {
            $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_INVOICE . "_{$key}}}",
                'label' => Mage::helper('sales')->__($var)
            );
        }
        return $variables;
    }

    public function getVarsOnHiden_Invoice() {
        $variableList = $this->getAllVars_Invoice();
        $variables = array();
        $varBlackList = $this->getHidenVarInvoice();
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        return $variables;
    }
    
    public function getVarsConfig_Invoice() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_INVOICE);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Invoice());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Invoice();
        }
    }

    public function getAllVars_Invoice_Items() {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        $table_name = $resource->getTableName('sales/invoice_item');
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='" . $table_name . "' "
            . "AND COLUMN_NAME not like '%base_%' "
            . "AND TABLE_SCHEMA = '{$dbName}'");
            
            
            //zend_debug::dump($variableList); die('vao invoice');
        //to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['COLUMN_COMMENT'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $var['COLUMN_NAME'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__("{$var['COLUMN_COMMENT']}")
                );
            }
        }
        //add additional vars
        foreach ($this->_additional_invoice_items_vars as $key => $var) {
            $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$key}}}",
                'label' => Mage::helper('sales')->__($var)
            );
        }
        
         //zeus edit 26/01
        
        $vrpc = 'tax_percent'; 
        $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$vrpc}}}",
                'label' => Mage::helper('sales')->__('Tax Percent')
            );
        
                 //end edit
        return $variables;
    }

    public function getVarsOnHiden_Invoice_Items() {
        $variableList = $this->getAllVars_Invoice_Items();
        $variables = array();
        $varBlackList = $this->getHidenVarInvoiceItem();
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        return $variables;
    }
    
    public function getVarsConfig_Invoice_Items() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_INVOICE_ITEMS);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Invoice_Items());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Invoice_Items();
        }
    }

    public function getAllVars_Creditmemo() {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        $table_name = $resource->getTableName('sales/creditmemo');
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='" . $table_name . "' "
            . "AND COLUMN_NAME not like '%base_%' "
            . "AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['COLUMN_COMMENT'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_CREDITMEMO . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $var['COLUMN_NAME'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_CREDITMEMO . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__("{$var['COLUMN_COMMENT']}")
                );
            }
        }
        //add additional vars
        foreach ($this->_additional_creditmemo_vars as $key => $var) {
            $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_CREDITMEMO . "_{$key}}}",
                'label' => Mage::helper('sales')->__($var)
            );
        }
        return $variables;
    }

    public function getVarsOnHiden_Creditmemo() {
        $variableList = $this->getAllVars_Creditmemo();
        $variables = array();
        $varBlackList = $this->getHidenVarCreditmemo();
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        return $variables;
    }
    
    public function getVarsConfig_Creditmemo() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_CREDITMEMO);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Creditmemo());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Creditmemo();
        }
    }

    public function getAllVars_Creditmemo_Items() {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        $table_name = $resource->getTableName('sales/creditmemo_item');
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='" . $table_name . "' "
            . "AND COLUMN_NAME not like '%base_%' "
            . "AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach ($variableList as $var) {
            if ($var['COLUMN_COMMENT'] == '') {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__(ucwords(str_replace('_', ' ', $var['COLUMN_NAME'])))
                );
            } else {
                $variables[] = array(
                    'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('sales')->__("{$var['COLUMN_COMMENT']}")
                );
            }
        }
        //add additional vars
        foreach ($this->_additional_creditmemo_items_vars as $key => $var) {
            $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$key}}}",
                'label' => Mage::helper('sales')->__($var)
            );
        }
        //zeus edit 26/01
        $vrpc = 'tax_percent'; 
        $variables[] = array(
                'value' => "{{var " . self::PRE_VAR_ITEMS . "_{$vrpc}}}",
                'label' => Mage::helper('sales')->__('Tax Percent')
            );
                
        //end edit
        return $variables;
    }

    public function getVarsOnHiden_Creditmemo_Items() {
        $variableList = $this->getAllVars_Creditmemo_Items();
        $variables = array();
        $varBlackList = $this->getHidenVarCreditmemoItem();
        foreach ($variableList as $var) {
            if (isset($var)) {
                continue;
            }
            $variables[] = $var;
        }
        return $variables;
    }

    public function getVarsConfig_Creditmemo_Items() {
        $vars_conf = $this->getVarsConfig(self::PATH_CONFIG_CREDITMEMO_ITEMS);
        if ($vars_conf) {
            $vars_conf = unserialize($vars_conf);
            //get vars with config
            if (isset($vars_conf['options'])) {
                $all_vars = $this->indexArray($this->getAllVars_Creditmemo_Items());
                $_vars = array();
                foreach ($vars_conf['options'] as $opt) {
                    $_vars[] = $all_vars[$opt];
                }
                return $_vars;
            } else {
                return array();
            }
        } else {
            //get default when haven't var in config
            return $this->getVarsOnHiden_Creditmemo_Items();
        }
    }

    public function getVarsConfig($path) {
        return Mage::getStoreConfig($path);
    }

    /*     * ********* */
    /* get main var for menu */

    public function getMainVars($type) {
        $pre = '';
        $main_var = array();
        switch ($type) {
            case 'customer':
                $pre = self::PRE_CUSTOMER;
                $main_var = $this->main_var_customer;
                break;
            case 'order':
                $pre = self::PRE_VAR_ORDER;
                $main_var = $this->main_var_order;
                break;
            case 'invoice':
                $pre = self::PRE_VAR_INVOICE;
                $main_var = $this->main_var_invoice;
                break;
            case 'creditmemo':
                $pre = self::PRE_VAR_CREDITMEMO;
                $main_var = $this->main_var_creditmemo;
                break;
            case 'order_items':
                $pre = self::PRE_VAR_ITEMS;
                $main_var = $this->main_var_order_item;
                break;
            case 'invoice_items':
                $pre = self::PRE_VAR_ITEMS;
                $main_var = $this->main_var_invoice_item;
                break;
            case 'creditmemo_items':
                $pre = self::PRE_VAR_ITEMS;
                $main_var = $this->main_var_creditmemo_item;
                break;
            default:
                return null;
        }
        //proccess bind var pre
        $bound_vars = array();
        foreach ($main_var as $var) {
            $bound_vars[] = '{{var ' . $pre . '_' . $var . '}}';
        }
        return $bound_vars;
    }

    //show only variables is main
    protected function maskVars($arr_main, $arr_data, $label_main = 'main', $label_more = 'more') {
        $more = array();
        $main = array();
        if (empty($arr_main) && count($arr_main) <= 0) {
            return array($label_main => $arr_data, $label_more => array());
        }
        foreach ($arr_data as $item) {
            if (in_array($item['value'], $arr_main)) {
                $main[] = $item;
            } else {
                $more[] = $item;
            }
        }
        $data = array($label_main => $main, $label_more => $more);
        return $data;
    }

    protected function getVars($type) {
        $helper = Mage::helper('pdfinvoiceplus/variable');
        switch ($type) {
            case 'customer':
                return $this->maskVars($this->getMainVars('customer'), $helper->getCustomerVars());
            case 'order':
                return $this->maskVars($this->getMainVars('order'), $helper->getOrderVars());
            case 'invoice':
                return $this->maskVars($this->getMainVars('invoice'), $helper->getInvoiceVars());
            case 'creditmemo':
                return $this->maskVars($this->getMainVars('creditmemo'), $helper->getCreditmemoVars());
            case 'order_items':
                return $this->maskVars($this->getMainVars('order_items'), $helper->getOrderItemVars());
            case 'invoice_items':
                return $this->maskVars($this->getMainVars('invoice_items'), $helper->getInvoiceItemVars());
            case 'creditmemo_items':
                return $this->maskVars($this->getMainVars('creditmemo_items'), $helper->getCreditmemoItemVars());
            default:
                return null;
        }
    }

    public function getOrderVarsData() {
        return array
            (
            'order' => array
                (
                'customer' => $this->getVars('customer'),
                'order' => $this->getVars('order'),
                'item' => $this->getVars('order_items')
            )
        );
    }

    public function getInvoiceVarsData() {
        return array
            (
            'invoice' => array
                (
                'customer' => $this->getVars('customer'),
                'invoice' => $this->getVars('invoice'),
                'item' => $this->getVars('invoice_items')
            )
        );
    }

    public function getCreditmemoVarsData() {
        return array
            (
            'creditmemo' => array
                (
                'customer' => $this->getVars('customer'),
                'creditmemo' => $this->getVars('creditmemo'),
                'item' => $this->getVars('creditmemo_items')
            )
        );
    }

    /**
     * get array var to hidden var list
     */
    protected function getHidenVarCustomer() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_customer, self::PRE_CUSTOMER));
    }

    protected function getHidenVarOrder() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_order, self::PRE_VAR_ORDER));
    }

    protected function getHidenVarOrderItem() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_order_items, self::PRE_VAR_ITEMS));
    }

    protected function getHidenVarInvoice() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_invoice, self::PRE_VAR_INVOICE));
    }

    protected function getHidenVarInvoiceItem() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_invoice_items, self::PRE_VAR_ITEMS));
    }

    protected function getHidenVarCreditmemo() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_creditmemo, self::PRE_VAR_CREDITMEMO));
    }

    protected function getHidenVarCreditmemoItem() {
        return $this->convertArrayKey($this->bindPrefixVars($this->hiden_vars_creditmemo_items, self::PRE_VAR_ITEMS));
    }

    /**
     * $var is array list of var name
     * return array
     */
    protected function convertArrayKey($var = array()) {
        $res = array();
        foreach ($var as $val) {
            $res[$val] = 1;
        }
        return $res;
    }
    
    protected function indexArray($arr = array()){
        $temp = array();
        foreach($arr as $var){
            $temp[$var['value']] = $var;
        }
        return $temp;
    }

    /**
     * add your custom variable name here
     * @var type 
     */
    protected function initAdditionalOrderVars() {
        $this->_additional_order_vars = array(
            'payment_method' => Mage::helper('pdfinvoiceplus')->__('Payment Method'),
            'shipping_method' => Mage::helper('pdfinvoiceplus')->__('Shipping Method'),
            'currency' => Mage::helper('pdfinvoiceplus')->__('Currency'),
            'billing_address' => Mage::helper('pdfinvoiceplus')->__('Billing Address'),
            'shipping_address' => Mage::helper('pdfinvoiceplus')->__('Shipping Address')
        );
        return $this;
    }

    /**
     * add your custom variable name here
     * @var type 
     */
    protected function initAdditionalInvoiceVars() {
        $this->_additional_invoice_vars = array(
            'payment_method' => Mage::helper('pdfinvoiceplus')->__('Payment Method'),
            'shipping_method' => Mage::helper('pdfinvoiceplus')->__('Shipping Method'),
            'currency' => Mage::helper('pdfinvoiceplus')->__('Currency'),
            'billing_address' => Mage::helper('pdfinvoiceplus')->__('Billing Address'),
            'shipping_address' => Mage::helper('pdfinvoiceplus')->__('Shipping Address')
        );
        return $this;
    }

    /**
     * add your custom variable name here
     * @var type 
     */
    protected function initAdditionalCreditmemoVars() {
        $this->_additional_creditmemo_vars = array(
            'payment_method' => Mage::helper('pdfinvoiceplus')->__('Payment Method'),
            'shipping_method' => Mage::helper('pdfinvoiceplus')->__('Shipping Method'),
            'currency' => Mage::helper('pdfinvoiceplus')->__('Currency'),
            'billing_address' => Mage::helper('pdfinvoiceplus')->__('Billing Address'),
            'shipping_address' => Mage::helper('pdfinvoiceplus')->__('Shipping Address')
        );
        return $this;
    }

    protected function initAdditionalOrderItemsVars() {
        $this->_additional_order_items_vars = array(
            'small_image' => Mage::helper('pdfinvoiceplus')->__('Product Image')
        );
        return $this;
    }

    protected function initAdditionalInvoiceItemsVars() {
        $this->_additional_invoice_items_vars = array(
            'small_image' => Mage::helper('pdfinvoiceplus')->__('Product Image')
        );
        return $this;
    }

    protected function initAdditionalCreditmemoItemsVars() {
        $this->_additional_creditmemo_items_vars = array(
            'small_image' => Mage::helper('pdfinvoiceplus')->__('Product Image')
        );
        return $this;
    }
    
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
     * @return array("{{var name_var}}", ...)
     */
    protected function bindPrefixVars($vars = array(), $prefix = ''){
        $arr = array();
        foreach ($vars as $var){
            $arr[] = '{{var '.$prefix.'_'.$var.'}}';
        }
        return $arr;
    }

    /**
     * 
     * @param array('var') $vars
     * @param string $prefix
     */
    protected function unbindPrefixVars($vars = array(), $prefix = ''){
        $arr = array();
        foreach ($vars as $var){
            $arr[] = str_replace($prefix.'_', '',$var);
        }
        return $arr;
    }

}
