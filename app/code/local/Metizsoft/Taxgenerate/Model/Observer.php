<?php

class Metizsoft_Taxgenerate_Model_Observer extends Varien_Object
{	
    public function setorderstatetax(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $i=0;
        foreach ($quote->getAllVisibleItems() as $quoteitem) {
           $quotetax[$i]['statetax'] = $quoteitem->getStatetax();
           $quotetax[$i]['countytax'] = $quoteitem->getCountrytax();
           $quotetax[$i]['citytax'] = $quoteitem->getCitytax();
           $i++;
        }
        $order = $observer->getEvent()->getOrder();
        $j=0;
        foreach($order->getAllVisibleItems() as $item){
            $item->setStatetax($quotetax[$j]['statetax']);
            $item->setCountrytax($quotetax[$j]['countytax']);
            $item->setCitytax($quotetax[$j]['citytax']);
            $item->save();
            $j++;
        }
    }
    
    public function setinvoicestatetax(Varien_Event_Observer $observer)
    {
        
        $order = $observer->getEvent()->getInvoice()->getOrder();
        $i=0;
        foreach ($order->getAllItems() as $orderitem) {
           $ordertax[$i]['statetax'] = $orderitem->getStatetax();
           $ordertax[$i]['countytax'] = $orderitem->getCountrytax();
           $ordertax[$i]['citytax'] = $orderitem->getCitytax();
           $i++;
        }
        
        
        $invoice = $observer->getEvent()->getInvoice();
        $j=0;
        foreach($invoice->getAllItems() as $item){
             //echo '<pre>';print_r($item->getData());exit;
            $item->setStatetax($ordertax[$j]['statetax']);
            $item->setCountrytax($ordertax[$j]['countytax']);
            $item->setCitytax($ordertax[$j]['citytax']);
            $j++;
           //echo '<pre>';print_r($item->getData());exit;
        }
        
    }
    
    public function customerLogin($observer)
     {
         $customer = $observer->getCustomer();
         $firsttime = 'firsttime';
         Mage::getSingleton('core/session')->setFirstTime($firsttime);
     }
}