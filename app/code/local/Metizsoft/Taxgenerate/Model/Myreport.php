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
 * Statetax model
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Model_Myreport extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'metizsoft_taxgenerate_statetax';
    const CACHE_TAG = 'metizsoft_taxgenerate_statetax';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'metizsoft_taxgenerate_statetax';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'statetax';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('metizsoft_taxgenerate/myreport');
    }

    /**
     * before save statetax
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Model_Statetax
     * @author Metizsoft Solutions
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save statetax relation
     *
     * @access public
     * @return Metizsoft_Taxgenerate_Model_Statetax
     * @author Metizsoft Solutions
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Metizsoft Solutions
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
