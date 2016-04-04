<?php

/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 7/17/15
 * Time: 10:50 AM
 */
class SM_XPos_Helper_RealTimeProduct extends Mage_Core_Helper_Abstract
{
    const PRODUCT_NEED_UPDATE = 'PRODUCT_NEED_UPDATE';
    const XPOS_NEW_KEY = 'XPOS_NEW_KEY';
    const NEW_DATA_PRODUCT = 'NEW';
    const FINISHED_DATA_PRODUCT = 'FINISHED';

    public function proNeedReload($productId, $storeId)
    {
        $this->setProductForUseReload($productId, $storeId);
        Mage::app()->getCache()->save(md5(microtime()), self::XPOS_NEW_KEY, array("xpos_cache"), 9999999);
        //$data = $this->getProductForUseReload();  // For test
    }

    public function setProductForUseReload($productId, $storeId)
    {
        $productOldNeedLoad = Mage::app()->getCache()->load(self::PRODUCT_NEED_UPDATE);
        if ($productOldNeedLoad == false) {
            $data = array();
            $data[ $productId ] = array();
        } else {
            $data = Mage::helper('core')->jsonDecode($productOldNeedLoad);
            $data[ $productId ] = array();
        }
        $dataJson = Mage::helper('core')->jsonEncode($data);
        Mage::app()->getCache()->save($dataJson, self::PRODUCT_NEED_UPDATE, array("xpos_cache"), 9999999);
    }

    public function getProductForUseReload()
    {
        $productOldNeedLoad = Mage::app()->getCache()->load(self::PRODUCT_NEED_UPDATE);

        return Mage::helper('core')->jsonDecode($productOldNeedLoad);
    }

    public function updateListProductFinished($dataProductFinished, $storeFinished)
    {
        $productForUseReload = $this->getProductForUseReload();
        if (is_array($dataProductFinished)) {
            foreach ($dataProductFinished as $productId => $storeId) {
                //$dataP = $productForUseReload[ $productId ];
                //if (!$productForUseReload[ $productId ]) {
                $productForUseReload[ $productId ][] = $storeFinished;
                //}
            }
            $dataJson = Mage::helper('core')->jsonEncode($productForUseReload);
            Mage::app()->getCache()->save($dataJson, self::PRODUCT_NEED_UPDATE, array("xpos_cache"), 9999999);

            return true;
        } else {
            return false;
        }
    }

    public function resetRealTimeUpdate()
    {
        Mage::app()->getCache()->remove(self::XPOS_NEW_KEY);
        Mage::app()->getCache()->remove(self::PRODUCT_NEED_UPDATE);
    }

    public function flushXPOSCache()
    {
        Mage::dispatchEvent('adminhtml_cache_flush_xpos');
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('xpos_cache'));
        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("The cache X-POS has been flushed."));
    }
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

}
