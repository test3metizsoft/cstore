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



class Mirasvit_Credit_Block_Adminhtml_Transaction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'transaction_id';
        $this->_controller = 'adminhtml_transaction';
        $this->_blockGroup = 'credit';

        $this->_updateButton('save', 'label', Mage::helper('credit')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('credit')->__('Delete'));

        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getFormHtml()
    {
        $html = parent::getFormHtml();

        if (!Mage::registry('current_transaction')->getCustomerId()) {
            $html .= $this->getLayout()->createBlock('credit/adminhtml_transaction_edit_customer')->toHtml();
        }

        return $html;
    }

    public function getTransaction()
    {
        if (Mage::registry('current_transaction') && Mage::registry('current_transaction')->getId()) {
            return Mage::registry('current_transaction');
        }
    }

    public function getHeaderText()
    {
        if ($transaction = $this->getTransaction()) {
            return Mage::helper('credit')->__("Edit Transaction '%s'", $this->htmlEscape($transaction->getName()));
        } else {
            return Mage::helper('credit')->__('Create New Transaction');
        }
    }
}
