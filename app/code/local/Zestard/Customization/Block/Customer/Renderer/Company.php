<?php
class Zestard_Customization_Block_Customer_Renderer_Company extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$company = array();
		$customer = Mage::getModel('customer/customer')->load($value);
		foreach ($customer->getAddresses() as $address)
		{
    		$company[] = $address->getCompany();
		}
		$companies = implode(',<br/>', $company);
		return $companies;
	}
}
?>
