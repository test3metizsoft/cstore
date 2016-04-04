<?php

class Metizsoft_Countrytax_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function calcRowTotal()
    {
        parent::calcRowTotal();
        if(Mage::getSingleton('customer/session')->isLoggedIn()){
        $address =  Mage::getSingleton('customer/session')->getCustomer()->getDefaultShippingAddress();
        
        $checkout = Mage::getSingleton('checkout/session')->getQuote();
        $shipAddress = $checkout->getShippingAddress();
            if($shipAddress && $shipAddress->getRegionCode() && $shipAddress->getPostcode() && $shipAddress->getCountry()){
                //echo '<pre>';print_r($shipAddress->getRegionCode());exit;
                //echo '<pre>';print_r($shipAddress->getPostcode());exit;
                //echo '<pre>';print_r($shipAddress->getCountry());exit;
                
                $product = $this->getProduct();
                $product->load($product->getId());
                
                $country = $shipAddress->getCountry();
                $state = $shipAddress->getRegionCode();
                
                $rows = array();
                foreach ($product->getCategoryIds() as $category):
                    $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
                    $sql = "Select * from protax where category_id = $category && state_id = '$state'";
                    $rows = $connection->fetchAll($sql);
                    break;
                endforeach;
                
                //echo '<pre>';print_r($product->getUnitinbox());exit;
                //echo '<pre>';print_r($product->getTaxunit());exit;
                
                $countrytax = 0;
                $statetax = 0;
                $citytax = 0;
                foreach ($rows as $row):
                    $postalcodes = explode(',', $row['city_id']);
                    if(in_array($shipAddress->getPostcode(), $postalcodes)){
                        $countrytax = $row['county_tax']*$product->getTaxunit();
                        $statetax = $row['state_tax']*$product->getTaxunit();
                        $citytax = $row['city_tax']*$product->getTaxunit();
                    }
                endforeach;
                
                //Set Country tax, State tax and City tax in 'sales_flat_quote_item' table
                $this->setCountrytax($countrytax);
                $this->setStatetax($statetax);
                $this->setCitytax($citytax);
                
                //chage baseTotoal of row
                $baseTotal = $this->getQty()*($this->getPrice() + $countrytax + $statetax + $citytax);
                
                $total = $this->getStore()->convertPrice($baseTotal);
                $this->setRowTotal($this->getStore()->roundPrice($total));
                $this->setBaseRowTotal($this->getStore()->roundPrice($baseTotal));
                return $this;
            }
        }
    }
}