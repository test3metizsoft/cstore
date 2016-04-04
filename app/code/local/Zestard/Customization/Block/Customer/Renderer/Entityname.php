<?php
class Zestard_Customization_Block_Customer_Renderer_Entityname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$entity = array();
		$customer = Mage::getModel('customer/customer')->load($value);
                
		foreach ($customer->getAddresses() as $address)
		{
                    $entity[] = $address->getEntityname();
		}
		$entitys = implode(',<br/>', $entity);
		return $entitys;
	}
}
?>
