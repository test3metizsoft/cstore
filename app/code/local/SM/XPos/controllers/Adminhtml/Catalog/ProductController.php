<?php
require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog' . DS . 'ProductController.php');
//require_once(BP . DS . 'app' . DS . 'code' . DS . 'core' . DS . 'Mage' . DS . 'Adminhtml' . DS . 'controllers' . DS . 'Sales' . DS . 'Order' . DS . 'CreateController.php');
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 7/17/15
 * Time: 11:00 AM
 */
class SM_XPos_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);

            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                if (isset($data['copy_to_stores'])) {
                    $this->_copyAttributesBetweenStores($data['copy_to_stores'], $product);
                }

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }
        Mage::dispatchEvent('update_real_time_after_save', array('productId' => $productId, 'storeId' => $storeId));

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    public function newXposAction()
    {
        $product = $this->_initProduct();

        $setId = $this->getRequest()->getParam('set', null);

        Mage::dispatchEvent('catalog_product_new_action', array('product' => $product));

        $this->loadLayout('adminhtml_catalog_product_newXpos');

        $block = $this->getLayout()->getBlock('product_edit');

        if(!$setId){
            $settingBlock = $this->getLayout()->createBlock('xpos/adminhtml_catalog_product_edit_settings');
            $block->setChild('settings', $settingBlock);
        }else{
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add groups without attributes

                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() || !$attribute->getIsRequired()) {
                        unset($attributes[$key]);
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }

                $groupAttributeBlock = $this->getLayout()->createBlock('xpos/adminhtml_catalog_product_edit_attributes')
                    ->setGroup($group)
                    ->setGroupAttributes($attributes);
                $block->setChild('group-' . $group->getId(), $groupAttributeBlock);

                $inventoryBlock = $this->getLayout()
                    ->createBlock('adminhtml/catalog_product_edit_tab_inventory')
                ->setTemplate('sm/xpos/index/catalog/product/tab/inventory.phtml');
                $block->setChild('inventory_block', $inventoryBlock);
            }

            $categoriesBlock = $this->getLayout()->createBlock('xpos/adminhtml_catalog_product_edit_categories')->setTemplate('sm/xpos/index/catalog/product/categories.phtml');
            $block->setChild('catalog.product.edit.tab.categories', $categoriesBlock);
        }


        $this->renderLayout();
    }

    public function saveXposAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $storeId        = $this->getRequest()->getParam('store');

        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);

            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                if (isset($data['copy_to_stores'])) {
                    $this->_copyAttributesBetweenStores($data['copy_to_stores'], $product);
                }

                $response->setMessage($this->__('The product has been saved.'));
                $response->setProduct($product->getData());
                $response->setProductId($productId);
            } catch (Mage_Core_Exception $e) {
                $response->setError(true);
                $response->setMessage($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $response->setMessage($e->getMessage());
            }
        }

        Mage::dispatchEvent('update_real_time_after_save', array('productId' => $productId, 'storeId' => $storeId));

        $this->getResponse()->setBody($response->toJson());

    }
}
