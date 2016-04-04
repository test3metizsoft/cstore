<?php
/**
 * Metizsoft_Taxgenerate extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Metizsoft
 * @package        Metizsoft_Taxgenerate
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Citytax admin block
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Block_Adminhtml_Citytax extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_citytax';
        $this->_blockGroup         = 'metizsoft_taxgenerate';
        parent::__construct();
        $this->_headerText         = Mage::helper('metizsoft_taxgenerate')->__('Citytax');
        $this->_updateButton('add', 'label', Mage::helper('metizsoft_taxgenerate')->__('Add Citytax'));

    }
}
