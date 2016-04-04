<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Store Credit & Refund
 * @version   1.0.0
 * @build     307
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Credit_Block_Adminhtml_Transaction_Edit_Customer extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('mst_credit/transaction/edit/customer.phtml');
    }

    public function getGridBlock()
    {
        if (!$this->hasGridBlock()) {
            $this->setData(
                'grid_block',
                $this->getLayout()->createBlock('credit/adminhtml_transaction_edit_customer_grid')
            );
            $this->getData('grid_block')->toHtml();
        }

        return $this->getData('grid_block');
    }
}
