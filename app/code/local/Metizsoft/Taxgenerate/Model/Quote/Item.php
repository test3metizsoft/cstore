<?php

class Metizsoft_Taxgenerate_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
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
                    $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                            ->addFieldToFilter(array('category_id'),array($category))
                            ->addFieldToFilter(array('state'),array("$state"));
                    $rows = $tax;
                    break;
                endforeach;
                
                $statetax = 0;
                $countytax = 0;
                $citytax = 0;
                foreach ($rows as $row):
                    
                    $postalcodes = explode(',', $row->getZipcode());
                    //echo $shipAddress->getPostcode();echo '<pre>';print_r($postalcodes);echo '</pre>';
                    if(in_array($shipAddress->getPostcode(), $postalcodes)){
                        
                        $statetax = $row->getStateTax()*$product->getTaxunit();
                        $countytax = $row->getCountyTax()*$product->getTaxunit();
                        $citytax = $row->getCityTax()*$product->getTaxunit();
                        //echo $row->getStateTax().' '.$row->getCountyTax().' '.$row->getCityTax().'<br>';
                    }
                endforeach;
                
                //Set Country tax, State tax and City tax in 'sales_flat_quote_item' table
                
                $this->setCountrytax($countytax);
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