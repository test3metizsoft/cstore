<?php

class SM_XPos_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function calcRowTotal()
    {
        parent::calcRowTotal();
        
        $product = $this->getProduct();
        
        
        
        if(Mage::getSingleton('adminhtml/session_quote')){
        
            //Mage::log($product->getId());
            
            $checkout = $this->getQuote();
            $shipAddress = $checkout->getShippingAddress();
            if($shipAddress && $shipAddress->getRegionCode() && $shipAddress->getPostcode() && $shipAddress->getCountry()){
                
                $product = $this->getProduct();
                $product->load($product->getId());

                //Set Country tax, State tax and City tax in 'sales_flat_quote_item' table
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
                //Mage::log($tax->getSelect()->__toString());
                foreach ($rows as $row):
                    $postalcodes = explode(',', $row->getZipcode());
                    Mage::log($shipAddress->getPostcode());
                    if(in_array($shipAddress->getPostcode(), $postalcodes)){
                        $statetax = $row->getStateTax()*$product->getTaxunit();
                        $countytax = $row->getCountyTax()*$product->getTaxunit();
                        $citytax = $row->getCityTax()*$product->getTaxunit();
                        //echo $row->getStateTax().' '.$row->getCountyTax().' '.$row->getCityTax().'<br>';
                    }
                endforeach;
                
                //Mage::log($tax->getSelect()->__toString());
                
                $this->setCountrytax($countytax);
                $this->setStatetax($statetax);
                $this->setCitytax($citytax);

                //$this->getQuote()->getShippingAddress()->getShippingDescription();
                //Mage::log($this->getQuote()->getShippingAddress()->getCountry());

                //chage baseTotoal of row
                Mage::log('Qty :'.$this->getQty().' Price : '.$this->getPrice().' State tax : '.$statetax .' Country : '.$countytax .' citytax : '.$citytax);
                $baseTotal = $this->getQty()*($this->getPrice() + $countytax + $statetax + $citytax);

                $total = $this->getStore()->convertPrice($baseTotal);
                $this->setRowTotal($this->getStore()->roundPrice($total));
                $this->setBaseRowTotal($this->getStore()->roundPrice($baseTotal));
            }
            return $this;
        }
    }
}