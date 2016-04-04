<?php
class Metizsoft_Taxgenerate_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $countyname = $this->getRequest()->getParam('county');
        $region = Mage::getModel('directory/region')->load($statecode);
        $region = $region->getCode();
        //echo '<pre>';print_r($region->getCode());exit;
        $counties  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                        array('name'), 
                        array("$countyname"));
        $countyarr = array();
        foreach ($counties as $county) {
            $countyarr[] = $county->getEntityId();
            break;
        }
        $newcityarry = array();
        foreach ($countyarr as $countyid):
            $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                ->addFieldToFilter(
                        array('county_id'), 
                        array($countyid));
            $newcityarry = array_merge($newcityarry, $cities->getData());
        endforeach;
        $cityhtml = "<option value=''>Please Select City</option>";
        foreach ($newcityarry as $city) {
            $cityhtml .= "<option value='" . $city['city'] . "'>" .  $city['city'] . "</option>";
        }
        echo $cityhtml;exit;
    }
    public function statecountyAction()
    {
        $statecode = $this->getRequest()->getParam('state');
        $region = Mage::getModel('directory/region')->load($statecode);
        $region = $region->getCode();
        //echo '<pre>';print_r($region->getCode());exit;
        $counties  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                        array('state'), 
                        array("$region"));
        $countyarr = array();
        $countyhtml = "<option value=''>Please Select County</option>";
        foreach ($counties as $county) {
            $countyhtml .= "<option value='" . $county->getName() . "'>" .  $county->getName() . "</option>";
        }
        echo $countyhtml;exit;
    }
    public function getcartdetailAction() {
        $block = $this->getLayout()->createBlock('checkout/cart_sidebar');
        $block->setTemplate('checkout/cart/sidebar_header_ajax.phtml');
        echo $block->toHtml();exit;
        
        $productarr = array();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        $cartItems = $quote->getAllVisibleItems();
        $Subtotal = Mage::helper('checkout')->getQuote()->getGrandTotal();
        $productarr['count'] = $count;
        $productarr['Subtotal'] = Mage::helper('checkout')->formatPrice($Subtotal);
        $productarr['html'] = '';
        foreach ($cartItems as $key=>$item)
        {
            $productId = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);
            $producturl = $product->getUrlModel()->getUrl($product);
            $img = (string)Mage::helper('catalog/image')->init($product, 'image')->resize(90, 90);
            $price = Mage::helper('checkout')->formatPrice($item->getProduct()->getPrice());
            $productarr['html'] .= '<li class="item odd">'
                    .'<a class="product-image" title="'.$item->getname().'" href="'.$producturl.'">'
                    .'<img alt="'.$item->getname().'" src="'.$img.'"></a>'
                    .'<p class="product-name"><a href="'.$producturl.'">'.$item->getname().'</a></p>'
                    .'<div class="product-details">'
                    .'<strong>'.$item->getQty().'</strong> x'
                    .'<span class="price">'.$price.'</span>'
                    .'</div>'
                    .'</li>';
            
        }
        echo json_encode($productarr);exit;
    }
    
    public function setaddressAction()
    {
        $id = $this->getRequest()->getParam('id');
        if($id){
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                foreach ($customerData->getAddresses() as $address)
                {
                    if($address->getId() == $id):
                        $address->setIsDefaultShipping(true);
                        $address->save();
                        
                        $quote = Mage::getSingleton('checkout/session')->getQuote();
                        $shipAddress = $quote->getShippingAddress();
                        $shippingAddress = $quote->getShippingAddress();
                        $shippingAddress->setStreet(array($address->getStreet(1)))
                           ->setCity($address->getCity())
                           ->setRegionId($address->getRegionId())
                           ->setPostcode($address->getPostcode())
                           ->save();
                    endif;
                }
            }
        }
    }
}
?>
