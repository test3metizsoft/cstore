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
 * Pdfinvoiceplus Model Entity TotalsRender Abstract
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer - Tit
 */
abstract class Magestore_Pdfinvoiceplus_Model_Entity_TotalsRender_Abstract extends Mage_Core_Model_Abstract {

    protected $_totals;
    protected $_source;
    protected $_html = '';
    protected $_template_id;
    protected $_block_total;
    protected $_var_prefix;
    protected $_area = 'frontend'; //or adminhtml
    protected $_totalCodeVars = array();

    public function __construct() {
        $this->_connectTotalCodeVar();
    }

    //set the source model order|invoice|creditmemo
    public function setSource($source) {
        $this->_source = $source;
        if (!Mage::registry('source_totals')) {
            Mage::register('source_totals', $this->_source);
        }
        return $this;
    }

    public function setHtml($html) {
        $this->_html = $html;
        return $this;
    }

    public function setTemplateId($id) {
        $this->_template_id = $id;
        return $this;
    }

    public function renderHtml($html = '') {
        
        if ($html != '') {
            $this->_html = $html;
        }
        $_html = $this->_html; //html text
        if ($this->_html == '') {
            return '';
        }
        $total_html = $this->_getTotalHtmlVars($_html);
        $total_totals = $this->_getTotalsVarArray();
        //merge total array
        $totals = array();
        foreach ($total_html as $var_name => $total) {
            if (isset($total_totals[$var_name])) {
                foreach ($total_totals as $key => $val) {
                    if ($key == $var_name) {
                        break;
                    }
                    if (isset($total_html[$key])) {
                        $total_html[$key]['value'] = $val['value'];
                    } else {
                        $totals[$key] = $val;
                    }
                    unset($total_totals[$key]);
                }
                $totals[$var_name]['value'] = $total_totals[$var_name]['value'];
                $totals[$var_name]['label'] = $total['label'];
                unset($total_totals[$var_name]);
            } else {
                $totals[$var_name] = $total;
            }
            unset($total_html[$var_name]);
        }
        if (count($total_totals) > 0) {
            $totals = array_merge($totals, $total_totals);
        }

        $dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        $dom->load($_html);
//        return $dom->__toString();
        /*
         * v1
         *
          $col_label = $dom->find('.col-total-label',0);
          if(!is_null($col_label)){
          $col_label_childs = $col_label->find('.totals-label');
          $col_label->innertext = ''; //reset inner html col
          }
          $col_value = $dom->find('.col-total-value',0);
          if(!is_null($col_value)){
          $col_value_childs = $col_value->find('.totals-value');
          $col_value->innertext = ''; //reset inner html col
          }
          if(is_null($col_label) || is_null($col_value)){
          return $this->_html;
          }
          //get idx vars html
          $idx = 0;
          $vars_idx = array();
          foreach ($col_value_childs as $child) {
          if (preg_match('/\{\{.*\}\}/', $child->innertext, $matched)) {
          $vars_idx[$matched[0]] = $idx;
          }
          $idx++;
          }
          $cur_idx = 0;

          $totals = $this->_translateLocalizationTotal($totals);

          foreach ($totals as $var_key => $total) {
          if (isset($vars_idx[$var_key])) {
          $row_label = $col_label_childs[$vars_idx[$var_key]];
          $row_label->innertext = $total['label'];
          //$row_label->style = "line-height:24px";
          $col_label->innertext .= $row_label;
          $row_value = $col_value_childs[$vars_idx[$var_key]];
          $row_value->innertext = $total['value'];
          //$row_value->style = "line-height:24px";
          $col_value->innertext .= $row_value;
          $cur_idx = $vars_idx[$var_key];
          } else {
          if (count($col_value_childs) <= 0) {
          $row_label = '<div class="totals-label color-text" style="padding:5px;width:100%;font-weight:bold">' . $total['label'] . '</div>';
          $row_value = '<div class="totals-value color-text" style="padding:5px">' . $total['value'] . '</div>';
          $col_label->innertext .= $row_label;
          $col_value->innertext .= $row_value;
          } else {
          $row_label = $col_label_childs[$cur_idx];
          $row_label->innertext = $total['label'];
          //$row_label->style = "line-height:24px";
          $row_value = $col_value_childs[$cur_idx];
          $row_value->innertext = $total['value'];
          //$row_value->style = "line-height:24px";
          $col_label->innertext .= $row_label;
          $col_value->innertext .= $row_value;
          }
          }

          }
         * * */
        /*
         * v2
         */
        $total_wrap = $dom->find('.body-total', 0);
        if (!is_null($total_wrap)) {
            $childs_rows = $total_wrap->find('.total-row');
            $total_wrap->innertext = ''; //reset inner html col
        } else {
            return $this->_html;
        }
        if (is_null($childs_rows)) {
            return $this->_html;
        }
        //get idx vars html
        $idx = 0;
        $vars_idx = array();
        foreach ($childs_rows as $child) {
            if (preg_match('/\{\{.*\}\}/', $child->find('.total-value', 0)->innertext, $matched)) {
                $vars_idx[$matched[0]] = $idx;
            }
            $idx++;
        }

        $cur_idx = 0;

        $totals = $this->_translateLocalizationTotal($totals);

        foreach ($totals as $var_key => $total) {
            if (isset($vars_idx[$var_key])) {
                $total_row = $childs_rows[$vars_idx[$var_key]];
                $total_row->children(0)->innertext = $total['label'];
                $total_row->children(1)->innertext = str_replace($var_key, $total['value'], $total_row->children(1)->innertext);
                $total_wrap->innertext .= $total_row->__toString();
                $cur_idx = $vars_idx[$var_key];
            } else {
                if (count($childs_rows) <= 0) {
                    $total_row = '<div class="total-row">'
                        . '<div class="total-label color-text">' . $total['label'] . '</div>'
                        . '<div class="total-value color-text">' . $total['value'] . '</div>'
                        . '</div>';
                    $total_wrap->innertext .= $total_row;
                } else {
                    $total_row = $childs_rows[$cur_idx];
                    $total_row->children(0)->innertext = $total['label'];
                    $total_row->children(1)->innertext = $total['value'];
                    $total_wrap->innertext .= $total_row->__toString();
                }
            }
        }

        //echo $dom->__toString(); die;
        return $dom->__toString();
    }

    /**
     * totals in source have
     * @param type $total
     */
    public function setTotals($total) {
        $this->_totals = $total;
        return $this;
    }

    abstract protected function _getBlockTotal();

    public function getTotals() {
        $this->_block_total = $this->_getBlockTotal();
        if (is_object($this->_block_total)) {
            return $this->_block_total->getTotals();
        }
        return array(new Mage_Sales_Model_Order_Total);
    }

    /**
     * Format total value based on order currency
     *
     * @param   Varien_Object $total
     * @return  string
     */
    protected function formatTotalValue($total) {
        if (!$total->getIsFormated()) {
            return Mage::helper('core')->currency($total->getValue(), true, false);
        }
        return $total->getValue();
    }

    /**
     * 
     * @param type $html
     * @return array("{{var name}} => array('label'=>'text','value'=>'{{var name}}')")
     */
    protected function _getTotalHtmlVars($html) {
        $dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        $dom->load($html);
        //get label array
        $rows_total = $dom->find(".body-total > .total-row");
        //merger key => value
        $arr_mer = array();
        foreach ($rows_total as $rowElement) {
            preg_match('/\{\{.*\}\}/', $rowElement->find('.total-value', 0)->innertext, $matched);
            $arr_mer[$matched[0]]['value'] = $matched[0];
            $arr_mer[$matched[0]]['label'] = $rowElement->find('.total-label', 0)->innertext;
        }
        return $arr_mer;
    }

    /* v1
      protected function _getTotalHtmlVars($html) {
      $dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
      $dom->load($html);
      //get label array
      $elLabel = $dom->find(".col-total-label > .totals-label");
      $labels = array();
      foreach ($elLabel as $elTotal) {
      $labels[] = $elTotal->innertext;
      }

      //get varname array
      $elVars = $dom->find(".col-total-value > .totals-value");
      $vars = array();
      foreach ($elVars as $elTotal) {
      preg_match('/\{\{.*\}\}/', $elTotal->innertext, $matched);
      $vars[] = $matched[0];
      }

      //merger key => value
      $arr_mer = array();
      $count = (count($labels) > count($vars)) ? count($labels) : count($vars);
      for ($i = 0; $i < $count; $i++) {
      if (!isset($vars[$i])) {
      $vars[$i] = '';
      }
      if (!isset($labels[$i])) {
      $labels[$i] = '';
      }

      $arr_mer[$vars[$i]] = array('label' => $labels[$i], 'value' => $vars[$i]);
      }
      return $arr_mer;
      }
     */

    protected function _connectTotalCodeVar() {
        $_code_var = array(
            'subtotal' => "subtotal",
            'shipping' => "shipping_amount",
            'discount' => "discount_amount",
            'grand_total' => "grand_total",
            'base_grandtotal' => "base_grand_total"
        );
        if (!isset($this->_var_prefix) || is_null($this->_var_prefix)) {
            $this->_var_prefix = '';
            return $this;
        }
        foreach ($_code_var as $key => $value) {
            $this->_totalCodeVars[$key] = '{{var ' . $this->_var_prefix . '_' . $value . '}}';
        }
        return $this;
    }

    /**
     * @return array("{{var name}} => array('label'=>'text','value'=>'12345')")
     */
    protected function _getTotalsVarArray() {
        $totals = $this->getTotals();
        $toTotals = array();
        foreach ($totals as $to) {
            if ($this->_area == 'adminhtml') {
                $key = (isset($this->_totalCodeVars[$to->getCode()])) ? $this->_totalCodeVars[$to->getCode()] : $to->getCode();
                $toTotals[$key] = array(
                    'label' => $to->getLabel(),
                    'value' => Mage::helper('core')->currency($to->getValue(), true, false)
                );
            } else {
                if (isset($this->_totalCodeVars[$to->getCode()])) {
                    $key = $this->_totalCodeVars[$to->getCode()];
                    $toTotals[$key] = array(
                        'label' => $to->getLabel(),
                        'value' => Mage::helper('core')->currency($to->getValue(), true, false)
                    );
                }
            }
        }

        return $toTotals;
    }

    protected function _translateLocalizationTotal($totals) {
        $localer = Mage::helper('pdfinvoiceplus/localization');
        $template = Mage::getModel('pdfinvoiceplus/template')->load($this->_template_id);
        $localer->setLocalization($template->getLocalization());
        foreach ($totals as $key => $tt) {
            $totals[$key]['label'] = $localer->translate($tt['label']);
        }
        return $totals;
    }

    public function __destruct() {
        if (Mage::registry('source_totals')) {
            Mage::unregister('source_totals');
        }
    }

}
