<?php

class SM_XPos_Model_Quote_Item extends Mage_Sales_Model_Quote_Item {

    public function calcRowTotal() {
        parent::calcRowTotal();
        $product = $this->getProduct();
        if (Mage::getSingleton('adminhtml/session_quote')) {
            //Mage::log($product->getId());

            $checkout = $this->getQuote();
            $shipAddress = $checkout->getShippingAddress();
            //echo '<pre>';print_r($shipAddress);exit;
            if ($shipAddress && $shipAddress->getRegionCode() && $shipAddress->getPostcode() && $shipAddress->getCountry()) {
                $product = $this->getProduct();
                //echo '<pre>';print_r($shipAddress->getRegionCode());echo '</pre>';
                //echo '<pre>';print_r(array_reverse($product->getCategoryIds()));exit;
                $product->load($product->getId());

                //Set Country tax, State tax and City tax in 'sales_flat_quote_item' table
                
                //echo $county;exit;
                $state = $shipAddress->getRegionCode();
                $rows = array();
                $iscounty = 0;
                
                $county = $shipAddress->getStatecounty();
                $city = $shipAddress->getCity();
                //echo $city;exit;    
                $countyid = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                            ->addFieldToSelect('entity_id')
                            ->addFieldToFilter(array('name'), array($county))
                            ->addFieldToFilter(array('state'), array("$state"))->getFirstItem();
                
                $countyid = (isset($countyid->getData()['entity_id']))?$countyid->getData()['entity_id']:0;
                $cityid = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                        ->addFieldToSelect('entity_id')
                        ->addFieldToFilter('county_id', $countyid)
                        ->addFieldToFilter(array('city'), array("$city"))->getFirstItem();
                $cityid = (isset($cityid->getData()['entity_id']))?$cityid->getData()['entity_id']:0;
                
               /* echo '<pre>';print_r($product->getCategoryIds());
                echo $cityid.'<br>';
                echo $countyid.'<br>';
                echo $state.'<br>';
                exit;*/
                
                $isstatetax = 0;
                $iscountytax = 0;
                $iscitytax = 0;
                foreach (array_reverse($product->getCategoryIds()) as $category):
                    $statetax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                            ->addFieldToFilter('category_id', $category)
                            ->addFieldToFilter('state', $state)->getFirstItem();
                    if(count($statetax) > 0 && $isstatetax==0){
                        $isstatetax = 1;
                        $rows['statetax'] = $statetax->getData();
                    }
                    
                    $countytax = Mage::getModel('metizsoft_taxgenerate/countytax')->getCollection()
                            ->addFieldToFilter('county_id', $countyid)
                            ->addFieldToFilter('category_id', $category)
                            ->addFieldToFilter('state', $state)->getFirstItem();
                    if(count($countytax) > 0 && $iscountytax==0){
                        $iscountytax = 1;
                        $rows['countytax'] = $countytax->getData();
                    }
                    
                    $citytax = Mage::getModel('metizsoft_taxgenerate/citytax')->getCollection()
                            ->addFieldToFilter('state', $state)
                            ->addFieldToFilter('county_id', $countyid)
                            ->addFieldToFilter('city_id', $cityid)
                            ->addFieldToFilter('category_id', $category)->getFirstItem();
                    if(count($citytax) > 0 && $iscitytax==0){
                        $iscitytax = 1;
                        $rows['citytax'] = $citytax->getData();
                    }
                endforeach;
                //echo '<pre>';print_r($rows);exit;
                //exit;
                $statetax = 0;
                $countytax = 0;
                $citytax = 0;
                //Mage::log($tax->getSelect()->__toString());
                //foreach ($rows as $row):
                if($rows){
                    
                    if(isset($rows['statetax'])){
                        if($rows['statetax']['taxtype'] == 'per'){
                            $statetax = (($this->getPrice()*$rows['statetax']['state_tax'])/100);
                        }else{
                            $statetax = $rows['statetax']['state_tax'] * $product->getTaxunit();
                        }
                    }
                    if(isset($rows['countytax'])){
                        if($rows['countytax']['taxtype'] == 'per'){
                            $countytax = (($this->getPrice()*$rows['countytax']['county_tax'])/100);
                        }else{
                            $countytax = $rows['countytax']['county_tax'] * $product->getTaxunit();
                        }
                    }
                    
                    if(isset($rows['citytax'])){
                        if($rows['countytax']['taxtype'] == 'per'){
                            $citytax = (($this->getPrice()*$rows['citytax']['city_tax'])/100);
                        }else{
                            $citytax = $rows['citytax']['city_tax'] * $product->getTaxunit();
                        }
                    }
                    
                    if($shipAddress->getOutofcityarea() == '9'){
                        $citytax = 0;
                    }
                }

                $this->setCountrytax($countytax);
                $this->setStatetax($statetax);
                $this->setCitytax($citytax);

                //chage baseTotoal of row
                //Mage::log('Qty :' . $this->getQty() . ' Price : ' . $this->getPrice() . ' State tax : ' . $statetax . ' Country : ' . $countytax . ' citytax : ' . $citytax);
                $baseTotal = $this->getQty() * ($this->getCalculationPrice() + $countytax + $statetax + $citytax);

                $total = $this->getStore()->convertPrice($baseTotal);
                $this->setRowTotal($this->getStore()->roundPrice($total));
                $this->setBaseRowTotal($this->getStore()->roundPrice($baseTotal));
            }
            return $this;
        }
    }

}
