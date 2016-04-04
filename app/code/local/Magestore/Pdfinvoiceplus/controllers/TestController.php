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
 * Pdfinvoiceplus Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_TestController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        //$mpdf = new mPDF();
        $top = '0';
        $bottom = '0';
        $left = '0';
        $right = '0';
        //$orientation = $this->getOrientation();
        $mpdf = new Mpdf_Magestorepdf('', 'A4', 8, '', $left, $right, $top, $bottom);
        $html = '
<html>
<head>
<style>
@page chapter2{
    size: auto;
    odd-header-name: html_MyHeader2;
    odd-footer-name: html_MyFooter2;
}

</style>
</head>
<body>

<div>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>
<p>Text of Chapter 2 Thìn</p>

</div>


<htmlpagefooter name="MyFooter2">
    <h1 style="background-color:red;position:relative;">page 2 222 {PAGENO}/{nbpg}</h1>
</htmlpagefooter>

<sethtmlpagefooter name="MyFooter2" value="on" show-this-page="{nbpg}" />
</body></html>
';
        
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    public function allVarsAction() {
        $helper = Mage::helper('pdfinvoiceplus/variable');
        $variables = array(
            'order' => array(
                'customer' => $helper->getCustomerVars(),
                'order' => $helper->getOrderVars(),
                'order_item' => $helper->getOrderItemVars()
            ),
            'invoice' => array(
                'customer' => $helper->getCustomerVars(),
                'invoice' => $helper->getInvoiceVars(),
                'invoice_item' => $helper->getInvoiceItemVars()
            ),
            'creditmemo' => array(
                'customer' => $helper->getCustomerVars(),
                'creditmemo' => $helper->getCreditmemoVars(),
                'creditmemo_item' => $helper->getCreditmemoItemVars()
            )
        );

        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }

    public function imageAction() {
        
    }

    public function htmlDomAction() {
        $html = Mage::getModel('pdfinvoiceplus/template')->load(1);
        $dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        $dom->load($html->getInvoiceHtml());
        //$col = $dom->find(".col-total-label");

        $col = $dom->find(".col-total-label");
        $col = $col[0];
        //$col->appendChild($childs[3]);
        //$child[0]->appendChild($child[0]->childNodes[3]);
        //echo print_r($col->childNodes(3)->outertext);
        //echo "============";
        //echo $col->__toString();
        $child = $col->children();
        //$col->appendChild($col->childNodes(3));

        echo $child[0]->__toString();
    }

}
