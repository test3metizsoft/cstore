<?php
 
class Metizsoft_Countrytax_Block_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    public function getLoadedProduct()
    {
        return true;
    }
}