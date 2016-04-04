<?php
class Zestard_Customization_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    /*protected function _prepareCollection()
	{
	    $collection = Mage::getResourceModel('customer/customer_collection')
	        ->addNameToSelect()
	        ->addAttributeToSelect('email')
	        ->addAttributeToSelect('created_at')
	        ->addAttributeToSelect('group_id')
	        ->addAttributeToSelect('customer_id')
	        ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
	        ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
	        ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
	        ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
	        ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

	    $this->setCollection($collection);
	    return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
	}*/
    
    protected function _prepareColumns()
    {
    	$this->addColumnAfter('company', array(
            'header'    => Mage::helper('customer')->__('Company Name'),
            'index'     => 'entity_id',
            'width'     => '450px',
            'renderer'  => 'Zestard_Customization_Block_Customer_Renderer_Company',
            'filter_condition_callback' => array($this, '_companyFilter')
        ), 'entity_id');
        $this->addColumnAfter('entityname', array(
            'header'    => Mage::helper('customer')->__('Entity name'),
            'index'     => 'entity_id',
            'width'     => '330px',
            'renderer'  => 'Zestard_Customization_Block_Customer_Renderer_Entityname',
            'filter_condition_callback' => array($this, '_entitynameFilter')
        ), 'company');
        return parent::_prepareColumns();
    }
    
    protected function _companyFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $customers = Mage::getModel('customer/customer')->getCollection();
        $companyId = array();
        foreach($customers as $customer){
            foreach ($customer->getAddresses() as $address)
            {   
                $stringValue = $address->getCompany();
                if(stripos($stringValue, $value) !== false){
                    $companyId[] = $customer->getEntityId();
                    break;
                }                    
            }		
        }
        if(!empty($companyId)){
            $this->getCollection()->addFieldToFilter("entity_id",array("in",$companyId));
        }        
        return $this;
    }
    
    protected function _entitynameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $customers = Mage::getModel('customer/customer')->getCollection();
        $companyId = array();
        foreach($customers as $customer){
            foreach ($customer->getAddresses() as $address)
            {   
                $stringValue = $address->getEntityname();
                if(stripos($stringValue, $value) !== false){
                    $companyId[] = $customer->getEntityId();
                    break;
                }                    
            }		
        }
        if(!empty($companyId)){
            $this->getCollection()->addFieldToFilter("entity_id",array("in",$companyId));
        }        
        return $this;
    }
}
?>