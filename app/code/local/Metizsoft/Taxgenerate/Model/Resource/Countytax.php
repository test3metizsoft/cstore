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
 * Countylist resource model
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Model_Resource_Countytax extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Metizsoft Solutions
     */
    public function _construct()
    {
        $this->_init('metizsoft_taxgenerate/countytax', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $countylistId
     * @return array
     * @author Metizsoft Solutions
     */
    public function lookupStoreIds($countylistId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('metizsoft_taxgenerate/countytax_store'), 'store_id')
            ->where('countylist_id = ?', (int)$countylistId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return Metizsoft_Taxgenerate_Model_Resource_Countylist
     * @author Metizsoft Solutions
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Metizsoft_Taxgenerate_Model_Countylist $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('taxgenerate_countytax_store' => $this->getTable('metizsoft_taxgenerate/countytax_store')),
                $this->getMainTable() . '.entity_id = taxgenerate_countytax_store.countylist_id',
                array()
            )
            ->where('taxgenerate_countytax_store.store_id IN (?)', $storeIds)
            ->order('taxgenerate_countytax_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Assign countylist to store views
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return Metizsoft_Taxgenerate_Model_Resource_Countylist
     * @author Metizsoft Solutions
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('metizsoft_taxgenerate/countytax_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'countylist_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'countylist_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }}
