<?php

class Metizsoft_Countrytax_Model_Observer extends Varien_Object
{	
    public function implementOrderStatus($event)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach($quote->getAllItems() as $quote_item) {
            $product = Mage::getModel('catalog/product')->load($quote_item->getProductId());
            foreach ($product->getCategoryIds() as $category):
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                $sql = "Select * from protax where category_id = $category && country_id = 'US' && state_id = 'FL'";
                $rows = $connection->fetchAll($sql);
                break;
            endforeach;
            //echo '<pre>';print_r($quote_item->getBaseRowTotal());exit;
            
            $productData  = $product->getData();
            //$quote_item->setItemcomment($rows[0]['country_tax']);
            //$quote_item->setBaseRowTotal($quote_item->getBaseRowTotal()+20);
            $baseTotal = 10;
        
            $total = $quote_item->getStore()->convertPrice($baseTotal);
            $quote_item->setRowTotal($quote_item->getStore()->roundPrice($total));
            $quote_item->setBaseRowTotal($quote_item->getStore()->roundPrice($baseTotal));
            //$quote_item->save();
        }
        $quote->save();
    }
}