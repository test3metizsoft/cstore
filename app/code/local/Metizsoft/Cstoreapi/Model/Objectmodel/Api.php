<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    CstoreApi
 * @package     Metizsoft_CstoreApi
 * @Author      Jaydip Kansagra
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Metizsoft_Cstoreapi_Model_ObjectModel_Api extends Mage_Api_Model_Resource_Abstract {

    public function getProducts($arg) {
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $products->setPage($arg['display_param']['PageName'], $arg['display_param']['PageSize']);

        foreach ($arg['sort'] as $x => $y) {
            $products->setOrder($x, $y);
        }
        foreach ($arg['products_param'] as $k => $v) {
            $products->addFieldToFilter($k, $v);
        }
        foreach ($products as $product) {
            $getdata = $product->getData();
            $getdata['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['image'];
            $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
            $getdata['thumbnail'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['thumbnail'];

            $productData[] = array(
                'product_id' => $product['entity_id'],
                'name' => $product['name'],
                'sku' => $product['sku'],
                'image' => $getdata['image'],
                'is_in_stock' => $product['is_in_stock'],
                'price' => $product['price'],
                'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                'description' => $product['description'],
                'additionalDescription' => $product['short_description']
            );
        }
        return $productData;
    }

    public function getCategories($arg) {
        $categorys = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('entity_id')->addAttributeToSelect('entity_type_id')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('description');

        $categorys->setPage($arg['category_param']['catid']);
        foreach ($arg as $k => $v) {
            $categorys->addFieldToFilter($k, $v);
        }

        foreach ($categorys as $category) {

            $categoryData[] = array(
                'entity_id' => $category['entity_id'],
                'entity_type_id' => $category['entity_type_id'],
                'parent_id' => $category['parent_id'],
                'position' => $category['position'],
                'level' => $category['level'],
                'children_count' => $category['children_count'],
                'name' => $category['name'],
                'description' => $category['description']
            );
        }
        return $categoryData;
    }

    public function getUsersubcat($arg) {
        $products = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*');
        foreach ($arg['category_param'] as $k => $v) {
            $products->addFieldToFilter($k, $v);
        }

        foreach ($products as $product) {
            $productData[] = $product->getData();
        }
        return $productData;
    }

    public function getCreate($customerData) {
        try {
            $customer = Mage::getModel('customer/customer')
                    ->setData($customerData)
                    ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $customer->getId();
    }

    public function getUserlist($user) {
        $products = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
        $products->setPage($user['customer_param']['customer_id'], $user['display_param']['PageSize']);
        foreach ($user as $k => $v) {
            $products->addFieldToFilter($k, $v);
        }
        foreach ($products as $product) {
            $productData[] = $product->getData();
        }
        return $productData;
    }

    public function getcartid($arg) {
        $customer_id = $arg['customer_id'];
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "Select * from sales_flat_quote WHERE customer_id = " . $customer_id . " AND is_active = 1 ORDER BY `store_id` DESC";
        $rows = $connection->fetchCol($sql);
        return $rows;
    }

    public function getList($customerdata) {
        $customer = Mage::getModel('customer/List')->getCollection()->addAttributeToSelect('*');
        $customer->setPage($arg['list_param']['customer_id']);
        foreach ($customers as $customer) {
            $customerdata[] = $customer->getData();
        }
        return $customerdata;
    }

    public function getUserinfo($arg) {
        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
        $result = array();

        foreach ($arg['user_detail'] as $k => $v) {
            $collection->addFieldToFilter($k, $v);
        }
        foreach ($collection as $customer) {
            $result[] = $customer->toArray();
        }
        return $result;
    }

    public function getOrder($salesorder) {
        $product = Mage::getModel('sales/order')->load($salesorder);
        $productData[] = $product->getData();
        return $productData;
    }

    public function getAddressbook($temp) {
        $customerCollection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*'); //Fetch all Customers from magento

        $defaultData = array();
        $finalData = array();
        foreach ($customerCollection as $cust) {
            $data = array();
            $customer = Mage::getModel('customer/customer')->load($cust->getId()); // Get Customer info from Customer Id
            $defaultData = $customer->getData(); //Get Customer data

            if (array_key_exists('default_billing', $defaultData)) {
                $billingAddress = Mage::getModel('customer/address')->load($defaultData['default_billing']);
                $data['default_billing'] = $billingAddress->getData();
            }

            if (array_key_exists('default_shipping', $defaultData)) {
                $shippingAddress = Mage::getModel('customer/address')->load($defaultData['default_shipping']);
                $data['default_shipping'] = $shippingAddress->getData();
            }
            $finalData[] = array_merge($customer->getData(), $data);
        }
        return $finalData;
    }

    public function getUserlogin($arg) {
        $customer_email = $_GET['email'];
        $password = $_GET['password_hash'];

        $customer = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
        foreach ($arg['user_param'] as $k => $v) {
            $products->addFieldToFilter($k, $v);
        };

        $customer = $customer->getData();
        return $customer;

        foreach ($arg['user_param'] as $k => $v) {
            $arg->addFieldToFilter($k, $v);
        }
    }

    public function getProductreview($arg) {
        $reviews = Mage::getModel('review/review')->getResourceCollection();

        foreach ($arg['review_detail'] as $k => $v) {
            $reviews->addFieldToFilter($k, $v);
        }
        $reviews = $reviews->getData();
        $i = 0;
        foreach ($reviews as $review) {
            $new_review[$i] = $review;
            $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
            $products->addFieldToFilter('entity_id', array('eq' => $review['entity_pk_value']));

            foreach ($products as $product) {
                $getdata = $product->getData();
                $getdata['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['image'];
                $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
                $getdata['thumbnail'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['thumbnail'];
                $productData = array(
                    'name' => $product['name'],
                    'product_id' => $product['entity_id'],
                    'price' => $product['price'],
                    'image' => $getdata['image'],
                    'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                    'description' => $product['description'],
                    'additionalDescription' => $product['short_description'],
                );
                $new_review[$i]['products'] = $productData;
            }
            $i++;
        }
        return $new_review;
    }

    public function getProductwishlist($arg) {
        $wishlist = Mage::getModel('wishlist/wishlist')->getResourceCollection();

        if (count($wishlist)) {
            $arrProductIds = array();

            foreach ($wishlist as $item) {
                $product = $item->getProduct(getId);
            }
        }
        $wishlist = $wishlist->getData();
        return $wishlist;
    }

    public function getSearchproduct($arg) {
        $defaultshipping = '';
        $categoryId = $arg['products_param']['category_id']; // a category id that you can get from admin
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $customer = Mage::getModel('customer/customer')->load($arg['customer_id']); // insert customer ID
        $customerAddressId = $customer->getDefaultShipping();
        if ($customerAddressId) {
            $address = Mage::getModel('customer/address')->load($customerAddressId);
            $defaultshipping = $address->getData();
            $region = Mage::getModel('directory/region')->load($defaultshipping['region_id']);
            $state = $region->getCode();
        }
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')
                        ->addCategoryFilter($category)->load($categoryId);
        //return $products;
        foreach ($arg['products_param'] as $k => $v) {
            //$products->addFieldToFilter($k, $v);
        }
        $rows = array();
        $productData = array();
        foreach ($products as $product) {
            if ($product['price'] > 0 && $product['status'] == '1'):
                if ($product->getCategoryIds()) {
                    foreach (array_reverse($product->getCategoryIds()) as $category):
                        $demo = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection();
                        $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                                ->addFieldToFilter(array('category_id'), array($category))
                                ->addFieldToFilter(array('state'), array("$state"));
                        if (count($tax) > 0) {
                            $rows = $tax;
                            break;
                        }
                    endforeach;
                }
                $statetax = 0;
                $countytax = 0;
                $citytax = 0;
                if ($product->getCategoryIds()) {
                    foreach ($rows as $row):
                        $postalcodes = explode(',', $row->getZipcode());
                        $postalcodes = explode(',', $row->getCity());
                        //Mage::log($shipAddress->getPostcode());

                        $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->load($row->getCity())->getData();
                        if (strtolower($defaultshipping['city']) == strtolower($cities['city'])) {
                            $statetax = $row->getStateTax() * $product->getTaxunit();
                            $countytax = $row->getCountyTax() * $product->getTaxunit();
                            $citytax = $row->getCityTax() * $product->getTaxunit();
                            if ($defaultshipping['outofcityarea'] == '9') {
                                $citytax = 0;
                            }
                        }
                    endforeach;
                }
                $getdata = $product->getData();
                $getdata['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['image'];
                $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
                $getdata['thumbnail'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['thumbnail'];
                $productData[] = array(
                    'product_id' => $product['entity_id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'image' => $getdata['image'],
                    'is_in_stock' => $product['is_in_stock'],
                    'price' => $product['price'],
                    'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                    'description' => $product['description'],
                    'statetax' => $statetax,
                    'countytax' => $countytax,
                    'citytax' => $citytax,
                    'price_with_tax' => array_sum(array($product['price'], $statetax, $countytax, $citytax))
                );
            endif;
        }
        return $productData;
    }

    public function getSearch($arg) {
        $defaultshipping = '';
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $products->setPage($arg['display_param']['PageName'], $arg['display_param']['PageSize']);
        $customer = Mage::getModel('customer/customer')->load($arg['customer_id']); // insert customer ID
        $customerAddressId = $customer->getDefaultShipping();

        if ($customerAddressId) {
            $address = Mage::getModel('customer/address')->load($customerAddressId);
            $defaultshipping = $address->getData();
            $region = Mage::getModel('directory/region')->load($defaultshipping['region_id']);
            $state = $region->getCode();
        }
        foreach ($arg['sort'] as $x => $y) {
            $products->setOrder($x, $y);
        }
        foreach ($arg['products_param'] as $k => $v) {
            $products->addFieldToFilter($k, $v);
        }
        foreach ($products as $product) {
            if ($product['price'] > 0 && $product['status'] == '1'):
                if ($product->getCategoryIds()) {
                    foreach ($product->getCategoryIds() as $category):
                        $demo = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection();
                        $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                                ->addFieldToFilter(array('category_id'), array($category))
                                ->addFieldToFilter(array('state'), array("$state"));
                        $rows = $tax;
                        break;
                    endforeach;
                }
                $statetax = 0;
                $countytax = 0;
                $citytax = 0;
                if ($product->getCategoryIds()) {
                    foreach ($rows as $row):
                        $postalcodes = explode(',', $row->getZipcode());
                        $postalcodes = explode(',', $row->getCity());

                        $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->load($row->getCity())->getData();

                        if (strtolower($defaultshipping['city']) == strtolower($cities['city'])) {
                            $statetax = $row->getStateTax() * $product->getTaxunit();
                            $countytax = $row->getCountyTax() * $product->getTaxunit();
                            $citytax = $row->getCityTax() * $product->getTaxunit();
                            if ($defaultshipping['outofcityarea'] == '9') {
                                $citytax = 0;
                            }
                        }
                    endforeach;
                }
                $getdata = $product->getData();
                $getdata['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['image'];
                $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
                $getdata['thumbnail'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['thumbnail'];

                $productData[] = array(
                    'product_id' => $product['entity_id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'image' => $getdata['image'],
                    'is_in_stock' => $product['is_in_stock'],
                    'price' => $product['price'],
                    'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                    'description' => $product['description'],
                    'statetax' => $statetax,
                    'countytax' => $countytax,
                    'citytax' => $citytax,
                    'price_with_tax' => array_sum(array($product['price'], $statetax, $countytax, $citytax)),
                    'qty' => 1,
                );
            endif;
        }
        return $productData;
    }
    public function getskuSearch($arg) {
        $defaultshipping = '';
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $products->setPage($arg['display_param']['PageName'], $arg['display_param']['PageSize']);
        $customer = Mage::getModel('customer/customer')->load($arg['customer_id']); // insert customer ID
        $customerAddressId = $customer->getDefaultShipping();

        if ($customerAddressId) {
            $address = Mage::getModel('customer/address')->load($customerAddressId);
            $defaultshipping = $address->getData();
            $region = Mage::getModel('directory/region')->load($defaultshipping['region_id']);
            $state = $region->getCode();
        }
        foreach ($arg['sort'] as $x => $y) {
            $products->setOrder($x, $y);
        }
        foreach ($arg['products_param'] as $k => $v) {
            $products->addFieldToFilter($k, $v);
        }
        foreach ($products as $product) {
            if ($product['price'] > 0 && $product['status'] == '1'):
                if ($product->getCategoryIds()) {
                    foreach ($product->getCategoryIds() as $category):
                        $demo = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection();
                        $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                                ->addFieldToFilter(array('category_id'), array($category))
                                ->addFieldToFilter(array('state'), array("$state"));
                        $rows = $tax;
                        break;
                    endforeach;
                }
                $statetax = 0;
                $countytax = 0;
                $citytax = 0;
                if ($product->getCategoryIds()) {
                    foreach ($rows as $row):
                        $postalcodes = explode(',', $row->getZipcode());
                        $postalcodes = explode(',', $row->getCity());

                        $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->load($row->getCity())->getData();

                        if (strtolower($defaultshipping['city']) == strtolower($cities['city'])) {
                            $statetax = $row->getStateTax() * $product->getTaxunit();
                            $countytax = $row->getCountyTax() * $product->getTaxunit();
                            $citytax = $row->getCityTax() * $product->getTaxunit();
                            if ($defaultshipping['outofcityarea'] == '9') {
                                $citytax = 0;
                            }
                        }
                    endforeach;
                }
                $getdata = $product->getData();
                $getdata['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['image'];
                $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
                $getdata['thumbnail'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['thumbnail'];
                //return $product->getData();
                $productData[] = array(
                    'product_id' => $product['entity_id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'image' => $getdata['image'],
                    'is_in_stock' => $product['is_in_stock'],
                    'price' => $product['price'],
                    'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                    'description' => $product['description'],
                    'short_description' => $product['short_description'],
                    'statetax' => $statetax,
                    'countytax' => $countytax,
                    'citytax' => $citytax,
                    'price_with_tax' => array_sum(array($product['price'], $statetax, $countytax, $citytax)),
                    'category_ids' => $product['category_ids'],
                    'visibility' => $product['visibility'],
                    'weight' => $product['weight'],
                    'status' => $product['status'],
                    'upccode' => $product['upccode'],
                    'retailupc' => $product['retailupc'],
                    'tax_class_id' => $product['tax_class_id'],
	            'boxincase' => $product['boxincase'],                    
	            'unitinbox' => $product['unitinbox'],
		    'taxunit' => $product['taxunit'],
                    'orgimage' => $product['image'],
                    'qty' => 1,
                );
            endif;
        }
        return $productData;
    }
    public function getProductdetail($arg) {
        $state = '';
        $statetax = 0;
        $countytax = 0;
        $citytax = 0;
        $defaultshipping = '';
        $customer = Mage::getModel('customer/customer')->load($arg['customer_id']); // insert customer ID
        $customerAddressId = $customer->getDefaultShipping();
        if ($customerAddressId) {
            $address = Mage::getModel('customer/address')->load($customerAddressId);
            $defaultshipping = $address->getData();
            $region = Mage::getModel('directory/region')->load($defaultshipping['region_id']);
            $state = $region->getCode();
        }
        $product = Mage::getModel('catalog/product');
        $product->load($arg['id']);

        if ($product->getCategoryIds()) {
            foreach ($product->getCategoryIds() as $category):
                $demo = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection();
                $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                        ->addFieldToFilter(array('category_id'), array($category))
                        ->addFieldToFilter(array('state'), array("$state"));
                $rows = $tax;
                break;
            endforeach;
        }

        if ($product->getCategoryIds()) {
            foreach ($rows as $row):
                $postalcodes = explode(',', $row->getZipcode());
                $postalcodes = explode(',', $row->getCity());

                $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->load($row->getCity())->getData();
                if (strtolower($defaultshipping['city']) == strtolower($cities['city'])) {
                    $statetax = $row->getStateTax() * $product->getTaxunit();
                    $countytax = $row->getCountyTax() * $product->getTaxunit();
                    $citytax = $row->getCityTax() * $product->getTaxunit();
                    if ($defaultshipping['outofcityarea'] == '9') {
                        $citytax = 0;
                    }
                }
            endforeach;
        }

        $imggallery = $product->getData('media_gallery');
        for ($i = 0; $i < count($imggallery['images']); $i++) {
            $gallery[$i] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $imggallery['images'][$i]['file'];
        }

        $data = array(
            'product_id' => $product->getData('entity_id'),
            'name' => $product->getData('name'),
            'sku' => $product->getData('sku'),
            'image' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getData('image'),
            'is_in_stock' => $product->getData('is_in_stock'),
            'price' => $product->getData('price'),
            'url_path' => Mage::getBaseUrl() . $product->getData('url_path'),
            'description' => $product->getData('description'),
            'media_gallery' => $gallery,
            'statetax' => $statetax,
            'countytax' => $countytax,
            'citytax' => $citytax,
            'price_with_tax' => array_sum(array($product['price'], $statetax, $countytax, $citytax))
        );
        if ($product->getData('entity_id')) {
            return $data;
        } else {
            throw new Exception('Product Not Found');
        }
        return $product->getData();
    }

    public function addNewItem($arg) {
        $session = Mage::getSingleton('customer/session');
        $session->loginById($arg['wishlist_param']['customer_id']);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $wishList = Mage::app()->setCurrentStore(1);
        $wishList = Mage::getSingleton('wishlist/wishlist')->loadByCustomer($customer);
        //  return $wishList;
        $productId = $arg['wishlist_param']['entity_id'];
        $product = Mage::getModel('catalog/product')->load($productId);
        ;
        $data = $wishList->addNewItem($product);
        return true;
    }

    public function addnewReview($arg) {
        $session = Mage::getSingleton('customer/session');
        $session->loginById($arg['review_param']['customer_id']);
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        $review = Mage::app()->setCurrentStore(1); //desired store id
        $review = Mage::getModel('review/review');
        $review->setEntityPkValue($arg['product_id']); //product id
        $review->setStatusId(1);
        $review->setTitle($arg['title']);
        $review->setDetail($arg['detail']);
        $review->setEntityId(1);
        $review->setStoreId(Mage::app()->getStore()->getId());
        $review->setStatusId(1); //approved
        $review->setCustomerId($arg['customer_id']); //null is for administrator
        $review->setNickname($arg['nickname']);
        $review->setReviewId($review->getId());
        $review->setStores(array(Mage::app()->getStore()->getId()));
        $review->save();
        $review->aggregate();
        $review = $review->getData();
        return true;
    }

    public function getcityitems() {
        $array1 = array(
            0 => array('Area_id' => 1, "AreaName" => "Cliffside Park", "City" => "North Jersey"),
            1 => array('Area_id' => 2, "AreaName" => "East Rutherford", "City" => "North Jersey"),
            2 => array('Area_id' => 3, "AreaName" => "Fairview", "City" => "North Jersey"),
            3 => array('Area_id' => 4, "AreaName" => "Lyndhurst", "City" => "North Jersey"),
            4 => array('Area_id' => 5, "AreaName" => "Bayonne", "City" => "North Jersey"),
            5 => array('Area_id' => 6, "AreaName" => "Hoboken", "City" => "North Jersey"),
            6 => array('Area_id' => 7, "AreaName" => "Jersey City", "City" => "North Jersey"),
            7 => array('Area_id' => 8, "AreaName" => "Kearny", "City" => "North Jersey"),
            8 => array('Area_id' => 9, "AreaName" => "Newark", "City" => "North Jersey"),
            9 => array('Area_id' => 10, "AreaName" => "Nutley", "City" => "North Jersey"),
            10 => array('Area_id' => 11, "AreaName" => "Weehawken", "City" => "North Jersey"),
            11 => array('Area_id' => 12, "AreaName" => "West New York", "City" => "North Jersey"),
            12 => array('Area_id' => 13, "AreaName" => "Secaucus", "City" => "North Jersey"),
            13 => array('Area_id' => 14, "AreaName" => "Union City", "City" => "North Jersey"),
            14 => array('Area_id' => 15, "AreaName" => "Rutherford", "City" => "North Jersey"),
            15 => array('Area_id' => 16, "AreaName" => "North Bergen", "City" => "North Jersey"),
            16 => array('Area_id' => 17, "AreaName" => "Manhattan", "City" => "NEW YORK"),
            17 => array('Area_id' => 18, "AreaName" => "Comming Soon..", "City" => "Central Jersey"),
        );
        return $array1;
        $i = 0;
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                echo $array3[$i] [$j];
            }
        }
        return $cars;
    }

    public function getcitynorthjerse() {
        $result = array(
            "Area" => array('1' => 'Cliffside Park', 'East Rutherford', 'Fairview', 'Lyndhurst',
                'Bayonne', 'Hoboken', 'Jersey City', 'Kearny', 'Newark', 'Nutley', 'Weehawken', 'West New York', 'Secaucus',
                'Union City', 'Rutherford', 'North Bergen')
        );

        return $result;
    }

    public function getcitynewyork() {
        $result = array(
            "Area" => array('1' => 'Manhattan')
        );

        return $result;
    }

    public function getcityjersey() {

        $result = array(
            "Area" => array('1' => 'Comming Soon.....')
        );
        return $result;
    }

    public function getWishList($arg) {
        $session = Mage::getSingleton('customer/session');
        $session->loginById($arg['customer_id']);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $wishList = Mage::getSingleton('wishlist/wishlist')->loadByCustomer($customer);

        $wishListItemCollection = $wishList->getItemCollection();
        foreach ($wishListItemCollection as $item) {
            $_product = $item->getProduct();

            $getdata = $item->getProduct();
            $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
            $_itemsInWishList[] = array(
                'name' => $_product['name'],
                'product_id' => $_product['entity_id'],
                'price' => $_product['price'],
                'image' => $_product['small_image'],
                'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                'description' => $_product['description'],
                'additionalDescription' => $_product['short_description'],
            );
        }

        return $_itemsInWishList;
    }

    public function getshoppingcart($arg) {
        $checkoutSession = Mage::getSingleton('customer/session');
        $checkoutSession->loginById($arg['cart_param']['customer_id']);

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('sales_flat_quote_item');

        $query = 'SELECT quote_id FROM ' . $table . '';

        return $query;
    }

    public function addcartCreate($customerId, $productId, $qty = 1, $arg) {
        $cart = Mage::app()->setCurrentStore(1); //desired store id
        $checkoutSession = Mage::getSingleton('customer/session');
        $checkoutSession->loginById($arg['cart_param']['customer_id']);
        $cart = Mage::getSingleton('checkout/cart')->getQuote();


        $productId = 1244;
        $product = Mage::getModel('catalog/product');
        $product->load($productId);
        $cart->addProduct($product, $params);
        $cart->save();
        $quote = Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

        $quote = $quote->getItemsCount(); //it returns '2' as expected
        return $quote;
    }

    public function getregion($arg) {
        $countryId = $arg['county_param']['country_id']; // a category id that you can get from admin
        $country = Mage::getModel('directory/country')->load($countryId);

        foreach ($country->getRegions() as $region) {
            $region->setName($region->getName());
            $result[] = $region->toArray(array('region_id', 'code', 'name'));
        }

        return $result;
    }

    public function getrootcategory($arg) {
        $categorys = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*');
        $categorys->setPage($arg['category_param']['catid']);
        foreach ($arg['category_param'] as $k => $v) {
            $categorys->addFieldToFilter($k, $v);
        }
        foreach ($categorys as $category) {

            if ($category['is_active'] == '1'):
                if ($category['entity_id'] != '288'):
                    //$categoryData[] = $category->getData();
                    $categoryData[] = array(
                        'entity_id' => $category['entity_id'],
                        'parent_id' => $category['parent_id'],
                        'is_active' => $category['is_active'],
                        'level' => $category['level'],
                        'children_count' => $category['children_count'],
                        'name' => $category['name']
                    );
                endif;
            endif;
        }
        return $categoryData;
    }

    public function getHomebanner($arg) {
        $categorys = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('entity_id')->addAttributeToSelect('name');
        $categorys->count();

        $i = 0;
        foreach ($categorys as $category) {
            // $categoryData[$i] = $category->getData();
            $categoryData[$i] = array(
                'entity_id' => $category['entity_id'],
                'name' => $category['name'],
            );

            $categoryData[$i]['count'] = Mage::getModel('catalog/category')->load($categoryData[$i]['entity_id'])->getProductCount();
            $category = $categoryData;
            $i++;
        }

        foreach ($category as $categories) {
            if ($categories['count'] > 5) {
                $categoiestest[] = $categories;
            }
        }
        $categoryData1 = array_rand($categoiestest, 9);
        $j = 0;
        foreach ($categoiestest as $key => $value) {
            if (in_array($key, $categoryData1)) {
                $value1[$j] = $value;
                $category = new Mage_Catalog_Model_Category();
                $category->load($value1[$j]['entity_id']);
                $collection = $category->getProductCollection();
                $collection->addAttributeToSelect('*');
                $l = 0;
                foreach ($collection as $_product) {
                    if ($l < 5) {
                        $productarray[$l] = $_product->getData();
                        $getdata = $_product->getData();
                        $getdata['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['image'];
                        $getdata['small_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['small_image'];
                        $getdata['thumbnail'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $getdata['thumbnail'];
//                          $productarray[$l] = $getdata;  
                        $productarray[$l] = array(
                            'product_id' => $getdata['entity_id'],
                            'name' => $getdata['name'],
                            'sku' => $getdata['sku'],
                            'image' => $getdata['image'],
                            'is_in_stock' => $getdata['is_in_stock'],
                            'price' => $getdata['price'],
                            'url_path' => Mage::getBaseUrl() . $getdata['url_path'],
                            'additionalDescription' => $getdata['short_description'],
                            'description' => $getdata['description']
                        );
                    }
                    $l++;
                }
                $value1[$j]['products'] = $productarray;
            }
            $j++;
        }
        return $value1;
    }

    public function changepassword($arg) {
        $customerId = $arg['customer_param']['customer_id'];
        $customer = Mage::getModel('customer/customer')->load($_POST['customer_id']);

        return $customer;
        $oldpassword = $_POST['password'];
        $passwordhash = $customer['password_hash'];
        $phasharray = explode(":", $passwordhash);
        $passpostfix = $phasharray[1];
        $completeOldPassword = $oldpassword . ":" . $passpostfix;
        if ($completeOldPassword == $passwordhash) {
            $customer->setPassword($_POST['newpass']);
            $customer->save();
        }
        return true;
    }

    public function getCartdata($arg) {
        $session = Mage::getSingleton('customer/session');
        $session->loginById($arg['customer_id']);
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        $cart = Mage::getSingleton('checkout/cart');
        foreach ($cart->getQuote()->getAllItems() as $item) {
            $products[$item->getProductId()] = $item->getProductId();
        }
        return $products;
        foreach ($cart->getAllItems() as $item) {

            $_product = $item->getProduct();

            $_itemsInWishList[] = $_product->getData();

            return $_itemsInWishList;
        }
    }

    public function getQuoteid($arg) {
        $customer_id = $arg['customer_id'];
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = "Select entity_id, customer_id from sales_flat_quote WHERE customer_id = " . $customer_id . " ORDER BY `store_id` DESC";
        $rows = $connection->fetchCol($sql);
        return $rows;
    }

    public function getLogin($arg) {
        $customer_id = $arg[0]['customer_id'];
        $customer_email = $arg[1]['email'];
        $customer_firstname = $arg[8]['firstname'];
        $customer_lastname = $arg[10]['lastname'];
        $customer_id_notfound = 0;

        //reserved_order_id

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $sql_customerid = "Select * from sales_flat_quote WHERE customer_id = " . $customer_id . " ORDER BY `store_id` DESC";
        $sql_orderid = "Select entity_id, reserved_order_id, customer_id from sales_flat_quote WHERE customer_id = " . $customer_id . " AND reserved_order_id != 'NULL' ORDER BY `store_id` DESC";
        $sql_orderid_null = "Select entity_id, reserved_order_id, customer_id from sales_flat_quote WHERE customer_id = " . $customer_id . " AND reserved_order_id IS NULL ORDER BY `store_id` DESC";

        $rows_customerid = $connection->fetchCol($sql_customerid);
        $rows_orderid = $connection->fetchCol($sql_orderid);
        $rows_orderid_null = $connection->fetchCol($sql_orderid_null);

        //return $rows_customerid;exit;

        $result_count = count($rows_customerid);
        $rows_count_orderid = count($rows_orderid);
        $rows_count_orderid_null = count($rows_orderid_null);

        if ($result_count == 0 || ($rows_count_orderid > 0 && $rows_count_orderid_null == 0)):
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $sql = "SELECT MAX(visitor_id) AS HighestPrice FROM log_customer";
            $rows = $connection->fetchCol($sql);
            $vistitor_id = $rows[0] + 1;

            /* Log Customer */
            $customerLog = Mage::getModel('log/customer')->loadByCustomer($customer_id);
            $customerLog->setVisitorId($vistitor_id);
            $customerLog->setCustomerId($customer_id);
            $customerLog->setStoreId('1');
            $customerlog_status = $customerLog->save();
            /* Log Customer */

            /* Log Quote */
            $customerQuote = Mage::getModel('sales/quote')->loadByCustomer($customer_id);
            $customerQuote->setStoreId('1');
            $customerQuote->setCustomerId($customer_id);
            $customerQuote->setCustomerEmail($customer_email);
            $customerQuote->setCustomerFirstname($customer_firstname);
            $customerQuote->setCustomerLastname($customer_lastname);
            $customerQuote_status = $customerQuote->save();

            $sql_insert = "SELECT MAX(entity_id) AS HighestPrice FROM sales_flat_quote";
            $rows_insert = $connection->fetchCol($sql_insert);

            /* Log Quote */


            /* Sales Flate Quote */
            /* Sales Flate Quote */

            if ($customerlog_status && $customerQuote_status):
                return $customer_id = 'Done';
            endif;

        endif;
    }

    public function getcounty($arg) {
        $statecode = $arg['state_id'];
        $region = Mage::getModel('directory/region')->load($statecode);
        $region = $region->getCode();
        $counties = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                array('state'), array("$region"));
        $countyhtml = array();
        $i = 0;
        foreach ($counties as $county) {
            $countyhtml[$i][county] = $county->getName();
            $i++;
        }
        return $countyhtml;
    }

    public function getcity($arg) {
        $countyname = $arg['county_id'];
        $region = Mage::getModel('directory/region')->load($statecode);
        $region = $region->getCode();
        $counties = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                array('name'), array("$countyname"));
        $countyarr = array();
        foreach ($counties as $county) {
            $countyarr[] = $county->getEntityId();
            break;
        }
        $newcityarry = array();
        $cityhtml = array();
        foreach ($countyarr as $countyid):
            $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                    ->addFieldToFilter(
                    array('county'), array($countyid));
            $newcityarry = array_merge($newcityarry, $cities->getData());
        endforeach;
        $i = 0;
        foreach ($newcityarry as $city) {
            $cityhtml[$i]['city'] = $city['city'];
            $i++;
        }
        return $cityhtml;
        echo $cityhtml;
        exit;
    }

    public function getclearcart($response) {
        foreach (Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item) {
            Mage::getSingleton('checkout/cart')->removeItem($item->getId())->save();
        }
        return TRUE;
    }

    public function clearCart($arg) {
        $store = Mage::getSingleton('core/store')->load(1);
        $quote = Mage::getModel('sales/quote')->setStore($store)->load($arg);
        $collection = $quote->getItemsCollection(false);
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                if ($item && $item->getId()) {
                    $quote->removeItem($item->getId());
                    $quote->collectTotals()->save();
                }
            }
        }
        return true;
    }
    public function getPendingOrders($arg) {
        $orderCollection = Mage::getResourceModel('sales/order_collection');
 
            $orderCollection->addFieldToFilter('status', 'pending')->getSelect();
            
            $i=0;
            foreach($orderCollection->getData() as $order)
            {
                $order = Mage::getModel("sales/order")->load($order['entity_id']);
                $test[$i]['orderdata'] = $order->getdata();
                $billingAddress = $order->getBillingAddress();
                $shippingAddress = $order->getShippingAddress();
                $payment = $order->getPayment()->getMethodInstance()->getTitle();
                $test[$i]['billing'] = $billingAddress->getdata();
                $test[$i]['shipping'] = $shippingAddress->getdata();
                $test[$i]['payment'] = $payment;
                
                $j=0;
                foreach($order->getAllItems() as $item){
                    $items[$j] = $item->getdata();
                    $j++;
                }
                $test[$i]['items'] = $items;
                
                $i++;
            }
            return $test;
    }
}

?>
