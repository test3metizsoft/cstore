<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Itemsorder extends Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator {

     public function processAllVars()
    {
        /* value and label */
        $varData = array();
        foreach ($this->getTheItems() as $item)
        {
            $allKeysLabel = array();
            $allKeys = array();
            $allVars = array();
            /* Change by Zeus 03/12 */
            foreach (array_keys($item) as $v)
            {
                if(isset($v) && isset($item[$v]['value']) && isset($item[$v]['label'])){
                    if(is_array($item[$v]['label']) && is_array($item[$v]['value']))
                $allKeysLabel['label_' . $v] = $item[$v]['value'] . ' ' . $item[$v]['label'];
                }
                if(isset($v) && isset($item[$v]['value'])){
                $allKeys[$v] = $item[$v]['value'];
                }
            }
            $allVars = array_merge($allKeysLabel, $allKeys);
            $varData[] = $allVars;
        }
            /* End change*/
        return $varData;
    }

    /**
     * Get the items for the source
     * @return array
     */
    public function getTheItems()
    {
        foreach ($this->getSource()->getAllItems() as $item)
        {
            $this->setItem($item);
            if ($item->getParentItem())
            {
                $theParent = $item->getParentItem();
                if (Mage::helper('pdfinvoiceplus/product')->isConfigurable($theParent->getProductId()))
                {
                    continue;
                }
                $isChild = true;
            } else
            {
                $isChild = false;
            }
             
            $imageData = Mage::helper('pdfinvoiceplus/product')->getTheProductImage($item->getProductId());
            if($item->isChildrenCalculated())
                $itemsPriceData = $this->isPriceDisplayOptions($item);
            else
                $itemsPriceData = array();
            $userAttributeData = Mage::helper('pdfinvoiceplus/product')->getDataAsVar(
                    $item->getProductId(), $this->getOrder()->getStoreId(), $isChild);

            $standardVars = $this->getSandardItemVars($item);
            $productioptions = $this->getItemOptions();
            if (isset($productioptions))
            {
                $attr[] = array_merge($itemsPriceData, $userAttributeData, $standardVars, $productioptions, $imageData);
            } else
            {
                $attr[] = array_merge($itemsPriceData, $userAttributeData, $standardVars, $imageData);
            }
            //}
        }
        return $attr;
    }
    public function getSandardItemVars($item)
    {
        $order = $this->getOrder();
        $productioptions = $this->getItemOptions();
        $items = $item->getData();
       // zend_debug::dump($items); die('vao order items');
        /* Change by Zeus 04/12 */
        $taxPercent = NULL;
        $taxAmount = NULL;
        $qtyrefunded = NULL;
        /* End change */
			if ($item->getTaxAmount() != 0)
            {
                if($item->isChildrenCalculated()){
                    if($item->getParentItem())
                        $taxAmount = $order->formatPriceTxt($item->getTaxAmount());
                    else
                        $taxAmount = ''; 
                }else{
                    $taxAmount = $order->formatPriceTxt($item->getTaxAmount());
                    }
                }
			if ($item->getTaxPercent() != 0)
            {
                if($item->isChildrenCalculated()){
                    if($item->getParentItem())
                        $taxPercent = number_format($item->getTaxPercent(),2,',','').'%';
                    else
                        $taxPercent = '';
                }else{
                    $taxPercent = number_format($item->getTaxPercent(),2,',','').'%';
                    }
            }
            
            //zend_debug::dump($taxPercent); die('vao order items');
            
            if ($item->getRowTotal() != 0)
            {
                if($item->isChildrenCalculated()){
                    if($item->getParentItem())
                        $rowTotal = $order->formatPriceTxt($item->getRowTotal());
                    else
                        $rowTotal = '';
                }else{
                    $rowTotal = $order->formatPriceTxt($item->getRowTotal());
                }
            }
            if ($item->getPrice() != 0)
            {
                if($item->isChildrenCalculated()){
                    if($item->getParentItem())
                        $price = $order->formatPriceTxt($item->getPrice());
                    else
                        $price = '';
                }else{
                    $price = $order->formatPriceTxt($item->getPrice());
                }
            }
//        $qtyordered =(int)$items['qty_ordered'];
//        $qtyinvoiced = (int)$items['qty_invoiced'];
//        $qtyrefunded = (int) $items['qty_refunded'];
        if($item->getQtyOrdered()!= 0){
            $qtyordered ='Ordered: '.(int)$item->getQtyOrdered();
        }
        if($item->getQtyInvoiced()!= 0){
            $qtyinvoiced ='Invoiced: '.(int)$item->getQtyInvoiced();
        }
        if($item->getQtyRefunded()!=0){
            $qtyrefunded ='Refunded: '.(int)$item->getQtyRefunded();
        }
        $itemSku =implode('<br/>',Mage::helper('catalog')->splitSku($item->getSku()));
        foreach ($items as $key => $value){
            $standardVars['items_'.$key]=array('value'=> $value);
            if($key =='qty_ordered'){
                $standardVars['items_qty_ordered'] =array(
                    'value' => $qtyordered
                ); 
            }
            if($key =='qty_invoiced'){
                $standardVars['items_qty_invoiced'] =array(
                    'value' => $qtyinvoiced 
                ); 
            }
            if($key =='qty_refunded'){
                $standardVars['items_qty_refunded'] =array(
                    'value' => $qtyrefunded
                ); 
            }
            if($key =='row_total'){
                $standardVars['items_row_total'] =array(
                    'value' => $rowTotal
                ); 
            }
            if($key =='price'){
                $standardVars['items_price']= array(
                    'value'=> $price
                );
            }
            if($key == 'original_price'){
                $standardVars['items_original_price']= array(
                    'value' => Mage::helper('core')-> currency($items['original_price'])
                );
            }
            if($key =='discount_amount'){
                $standardVars['items_discount_amount'] = array(
                    'value' => Mage::helper('core')-> currency($items['discount_amount'])
                );
            }
            //edit by Zeus 15/01/2014
             if($key =='tax_amount'){
                if($taxAmount){
                    $standardVars['items_tax_amount'] =array(
                     'value' => $taxAmount
                );
                }else{
                $standardVars['items_tax_amount'] =array(
                     'value' => $taxAmount = $order->formatPriceTxt(0)
                ); 
                }
            }
            //End edit
			if($key =='tax_percent'){
                $standardVars['items_tax_percent'] =array(
                     'value' => $taxPercent
                ); 
            }
            if($key =='product_options'){
                $standardVars['items_product_options'] =array(
                    'value' => $productioptions
                ); 
            }
            if($key =='sku'){
                $standardVars['items_sku'] =array(
                    'value' => $itemSku
                ); 
            }
        }
         //zend_debug::dump($standardVars); die('vao invoice items');
        return $standardVars;
    }

    /**
     * Get the Item prices for display - need to review this part adn move the item system to do
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        $order = $this->getOrder();
        $store = $this->getSource()->getStore();
        $item = $this->getItem();
        foreach ($item->getData() as $key => $value){
            $price['items_'.$key] = array('value' => $value );
            if($key == 'price_incl_tax'){
                $price['items_price_incl_tax']= array(
                   'value' => $order->formatPriceTxt($item->getPriceInclTax())
                );
            }
            if($key == 'row_total_incl_tax'){
                $price['items_row_total_incl_tax']= array(
                   'value' => $order->formatPriceTxt($item->getRowTotalInclTax())
                );
            }
        }
        return $price;
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions())
        {
            $result = Mage::helper('pdfinvoiceplus/items')->getItemOptions($options);
        }
        return $result;
    }

    /**
     * Return item Sku
     *
     * @param  $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku'))
            return $item->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
    }

    public function isPriceDisplayOptions($item = null)
    {
        if ($item)
        {
            if ($this->isChildCalculated($item))
            {
                return $itemsPriceData = $this->getItemPricesForDisplay();
            } else
            {
                return $itemsPriceData = array();
            }
        } else
        {
            return null;
        }
    }
    
    public function getAttributes()
    {
        return Mage::helper('pdfinvoiceplus/product')->getDataAsVar();
    }
}

?>