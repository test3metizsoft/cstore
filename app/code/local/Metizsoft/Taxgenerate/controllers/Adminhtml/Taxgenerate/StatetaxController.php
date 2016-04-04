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
 * Statetax admin controller
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Adminhtml_Taxgenerate_StatetaxController extends Metizsoft_Taxgenerate_Controller_Adminhtml_Taxgenerate
{
    /**
     * init the statetax
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Model_Statetax
     */
    protected function _initStatetax()
    {
        $statetaxId  = (int) $this->getRequest()->getParam('id');
        $statetax    = Mage::getModel('metizsoft_taxgenerate/statetax');
        if ($statetaxId) {
            $statetax->load($statetaxId);
        }
        Mage::register('current_statetax', $statetax);
        return $statetax;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Tax Generate'))
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Statetaxs'));
        $this->renderLayout();
    }
    public function countyAction()
    {
        $statecode = $this->getRequest()->getParam('state');
        $counties  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                        array('state'), 
                        array("$statecode"));
        
        $countyhtml = "<option value=''>Please Select</option>";
        foreach ($counties as $county) {
            $countyhtml .= "<option value='" . $county->getEntityId() . "'>" .  $county->getName() . "</option>";
        }
        echo $countyhtml;
    }
    
    
    public function taxAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Tax'));

        $this->_showLastExecutionTime(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE, 'tax');

        $this->_initAction()
            ->_setActiveMenu('report/sales/tax')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tax'), Mage::helper('adminhtml')->__('Tax'));

        $gridBlock = $this->getLayout()->getBlock('report_sales_tax.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
    public function cityAction()
    {
        $countyid = $this->getRequest()->getParam('county');
        $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                ->addFieldToFilter(
                        array('county_id'), 
                        array($countyid));
        $cityhtml = "<option value=''>Please Select</option>";
        foreach ($cities as $city) {
            $cityhtml .= "<option value='" . $city->getEntityId() . "'>" .  $city->getCity() . "</option>";
        }
        echo $cityhtml;
    }
    public function zipcodeAction()
    {
        $cityid = $this->getRequest()->getParam('cityid');
        $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                ->addFieldToFilter(
                        array('entity_id'), 
                        array($cityid));
        foreach ($cities as $city) {
            $cityhtml = $city->getzipcodes();
        }
        echo $cityhtml;
    }
    
    // Get county for admin side customer address
    public function countyadminaddAction()
    {
        $stateid = $this->getRequest()->getParam('stateid');
        $region = Mage::getModel('directory/region')->load($stateid);
        $statecode = $region->getCode();
        
        $counties  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                        array('state'), 
                        array("$statecode"));
        
        $countyhtml = "<option value=''>Please Select</option>";
        foreach ($counties as $county) {
            $countyhtml .= "<option value='" . $county->getName() . "'>" .  $county->getName() . "</option>";
        }
        echo $countyhtml;
    }
    public function cityadminaddAction()
    {
        $countyname = $this->getRequest()->getParam('county');
        
        $counties  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                ->addFieldToFilter(
                        array('name'), 
                        array("$countyname"));
        $countyarr = array();
        foreach ($counties as $county) {
            $countyarr[] = $county->getEntityId();
            break;
        }
        $newcityarry = array();
        foreach ($countyarr as $countyid):
            $cities = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                ->addFieldToFilter(
                        array('county_id'), 
                        array($countyid));
            $newcityarry = array_merge($newcityarry, $cities->getData());
        endforeach;
        $cityhtml = "<option value=''>Please Select City</option>";
        foreach ($newcityarry as $city) {
            $cityhtml .= "<option value='" . $city['city'] . "'>" .  $city['city'] . "</option>";
        }
        echo $cityhtml;exit;
    }
    function searchcat($arr, $a, $catname)
    {
        $r = array();
        $i = 0;
        foreach ($arr as $key=>$test) {
            if ($test['catids'] == $a) {
                $r[$i] = $test;
                $r[$i]['catname'] = $catname;
                $i = $i+1;
            }
        }

        return $r;
    }
    
    public function gettaxreportAction()
    {
        $savetax = Mage::getModel('metizsoft_taxgenerate/myreport')->getCollection();
        echo '<pre>';print_r($savetax->getData());exit;
       $header = array(
           0=>array('orderid'=>'Orderid',
               'ordernumber'=>'Ordernumber',
               'createddate'=>'Created',
               'shippingstate'=>'State',
               'shippingcounty'=>'County',
               'shippingcity'=>'City',
               'proname'=>'Product Name',
               'purchaseqty'=>'Orderd Qty',
               'statetax'=>'Statetax',
               'countrytax'=>'Countrytax',
               'citytax'=>'Citytax',
               'proid'=>'Product id',
               'catids'=>'Catids',
               'catname'=>'Catname'));
       
       $OrderProducts = Mage::getResourceModel('sales/order_collection')
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('state', 'complete');
       $i=1;
       $j=0;
       $orderdata = array();
        foreach ($OrderProducts as $key=>$_order):
            
            foreach($_order->getAllItems() as $item) {
                $orderdata[$j]['orderid'] = $_order->getId();
                $orderdata[$j]['ordernumber'] = $_order->getRealOrderId();
                $orderdata[$j]['createddate'] = $_order->getCreatedAt();
                $orderdata[$j]['shippingstate'] = $_order->getShippingAddress()->getRegion();
                $orderdata[$j]['shippingcounty'] = $_order->getShippingAddress()->getStatecounty();
                $orderdata[$j]['shippingcity'] = $_order->getShippingAddress()->getCity();
                $orderdata[$j]['proname'] = $item->getName();
                $orderdata[$j]['purchaseqty'] = $item->getQtyOrdered();
                $orderdata[$j]['statetax'] = $item->getStatetax();
                $orderdata[$j]['countrytax'] = $item->getCountrytax();
                $orderdata[$j]['citytax'] = $item->getCitytax();
                $orderdata[$j]['proid'] = $item->getProductId();
                $product = $item->getProduct();
                $cats = $product->getCategoryIds();
                $orderdata[$j]['catids'] = isset($cats[0])?$cats[0]:0;
                $j = $j+1;
            }
            
            $i=$i+1;
        endforeach;
       $uniccategory = array_unique(array_column($orderdata, 'catids'));
       $new = array();
       $k=0;
       foreach ($uniccategory as $cat):
           $catname = Mage::getModel('catalog/category')->load($cat)->getName();
       
           $new[$k] = $this->searchcat($orderdata, $cat, $catname);
           $k = $k+1;
       endforeach;
       
       $saveincsv = array_merge($header,call_user_func_array('array_merge', $new));
       
       $file_path = "taxreport.csv";
       $mage_csv = new Varien_File_Csv();
       $mage_csv->saveData($file_path, $saveincsv);
       echo 'Done';
       
    }
    // Get county for admin side customer address
    
    public function gettaxAction()
    {
        
        $itemid = $this->getRequest()->getParam('itemid');
        $stateid = $this->getRequest()->getParam('state');
        $zipcode = $this->getRequest()->getParam('zipcode');
        $city = $this->getRequest()->getParam('city');
        $ooca = $this->getRequest()->getParam('ooca');
        $region = Mage::getModel('directory/region')->load($stateid);
        $stateid = $region->getCode();
        
        $city = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                    ->addFieldToFilter(array('city'),array($city));
        //echo '<pre>';print_r($city->getData()[0]['entity_id']);exit;
        
        $product = Mage::getModel('catalog/product')->load($itemid);
        //echo $stateid;exit;
        foreach ($product->getCategoryIds() as $category):
            $cityid = (isset($city->getData()[0]['entity_id']))?$city->getData()[0]['entity_id']:0;
            $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                    ->addFieldToFilter(array('category_id'),array($category))
                    ->addFieldToFilter(array('city'),array($cityid))
                    ->addFieldToFilter(array('state'),array("$stateid"));
            $rows = $tax;
            break;
        endforeach;
        
        $statetax = 0;
        $countytax = 0;
        $citytax = 0;
        foreach ($rows as $row):
            
            //$postalcodes = explode(',', $row->getZipcode());
            //if(in_array($zipcode, $postalcodes)){
                $statetax = $row->getStateTax()*$product->getTaxunit();
                $countytax = $row->getCountyTax()*$product->getTaxunit();
                $citytax = 0;
                if($ooca == 10){
                    $citytax = $row->getCityTax()*$product->getTaxunit();
                }
            //}
            
        endforeach;
        $taxes = array('statetax'=>$statetax,'countytax'=>$countytax,'citytax'=>$citytax);
        echo Mage::helper('core')->jsonEncode($taxes);;
    }
    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit statetax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function editAction()
    {
        $statetaxId    = $this->getRequest()->getParam('id');
        $statetax      = $this->_initStatetax();
        if ($statetaxId && !$statetax->getId()) {
            $this->_getSession()->addError(
                Mage::helper('metizsoft_taxgenerate')->__('This statetax no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getStatetaxData(true);
        if (!empty($data)) {
            $statetax->setData($data);
        }
        Mage::register('statetax_data', $statetax);
        $this->loadLayout();
        $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Tax Generate'))
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Statetaxs'));
        if ($statetax->getId()) {
            $this->_title($statetax->getCityTax());
        } else {
            $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Add statetax'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new statetax action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save statetax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('statetax')) {
            try {
                $statetax = $this->_initStatetax();
                $statetax->addData($data);
                $statetax->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Statetax was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $statetax->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setStatetaxData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was a problem saving the statetax.')
                );
                Mage::getSingleton('adminhtml/session')->setStatetaxData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Unable to find statetax to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete statetax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $statetax = Mage::getModel('metizsoft_taxgenerate/statetax');
                $statetax->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Statetax was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting statetax.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Could not find statetax to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete statetax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massDeleteAction()
    {
        $statetaxIds = $this->getRequest()->getParam('statetax');
        if (!is_array($statetaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select statetaxs to delete.')
            );
        } else {
            try {
                foreach ($statetaxIds as $statetaxId) {
                    $statetax = Mage::getModel('metizsoft_taxgenerate/statetax');
                    $statetax->setId($statetaxId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Total of %d statetaxs were successfully deleted.', count($statetaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting statetaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massStatusAction()
    {
        //echo '<pre>';print_r($_REQUEST);exit;
        $statetaxIds = $this->getRequest()->getParam('statetax');
        if (!is_array($statetaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select statetaxs.')
            );
        } else {
            try {
                foreach ($statetaxIds as $statetaxId) {
                $statetax = Mage::getSingleton('metizsoft_taxgenerate/statetax')->load($statetaxId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d statetaxs were successfully updated.', count($statetaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating statetaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    
    function checkarray($findarray, $listarray){
        foreach ($findarray as $find):
            if(!in_array($find, $listarray)){
                return $find;
            }
        endforeach;
        return true;
    }
    
    public function massCsvAction()
    {
        
        $csvfile = $this->getRequest()->getParam('csv');
        if (!$csvfile) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select statetaxs.')
            );
        } else {
            try {
                $csv = new Varien_File_Csv;
                $csvpath = Mage::getBaseDir() . DS . 'var' . DS . 'import' . DS . $csvfile;
                
                $datas = $csv->getData($csvpath);
                $haverow = isset($datas[0])?$datas[0]:0;
                if($haverow):
                    
                    $checkvalueinarray = $this->checkarray(
                            array('statetax', 'category', 'taxtype', 'statetax', 'state'), 
                            $haverow);
                    if($checkvalueinarray != 1){
                        Mage::getSingleton('adminhtml/session')->addError(
                            Mage::helper('metizsoft_taxgenerate')->__('You forgot column of '.$checkvalueinarray)
                        );
                        $this->_redirect('*/*/index');
                        return;
                    }
                    
                    $csvdata = array();
                    foreach ($datas as $k=>$data) {
                        if($k==0){continue;}
                        foreach ($data as $key=>$row) {
                            $csvdata[$k][$haverow[$key]] = $row;
                        }
                    }
                    
                    foreach ($csvdata as $data) {
                        
                        $categoryid = Mage::getModel('catalog/category')->getCollection()
                                ->addFieldToFilter('name', $data['category']);
                        $categoryid = $categoryid->getFirstItem()->getId();
                        if(!$categoryid){
                            continue;
                        }
                        $tax = Mage::getModel('metizsoft_taxgenerate/statetax')->getCollection()
                            ->addFieldToFilter('category_id', $categoryid)
                            ->addFieldToFilter('state', $data['state'])->getFirstItem();
                        
                        $status = (isset($data['status'])?$data['status']:1);
                        $updated_at = (isset($data['updated_at'])?$data['updated_at']:date('Y-m-d H:i:s'));
                        $created_at = (isset($data['created_at'])?$data['created_at']:date('Y-m-d H:i:s'));
                            
                        if($tax->getData()){
                            $taxdata = $tax->getData();
                            
                            $savetax = Mage::getModel('metizsoft_taxgenerate/statetax')->load($taxdata['entity_id']);
                            $savetax->setCategoryId($categoryid);
                            $savetax->setState($data['state']);
                            $savetax->setTaxtype($data['taxtype']);
                            $savetax->setStateTax($data['statetax']);
                            $savetax->setStatus($status);
                            $savetax->setUpdatedAt($updated_at);
                            $savetax->save();
                        }else{
                            $savetax = Mage::getModel('metizsoft_taxgenerate/statetax');
                            $savetax->setCategoryId($categoryid);
                            $savetax->setState($data['state']);
                            $savetax->setTaxtype($data['taxtype']);
                            $savetax->setStateTax($data['statetax']);
                            $savetax->setStatus($status);
                            $savetax->setUpdatedAt($updated_at);
                            $savetax->setCreatedAt($created_at);
                            $savetax->save();
                        }
                        
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d statetaxs were successfully updated.', count($csvdata))
                    );
                    
                endif;
                
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating statetaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function exportCsvAction()
    {
        $fileName   = 'statetax.csv';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_statetax_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function exportExcelAction()
    {
        $fileName   = 'statetax.xls';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_statetax_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function exportXmlAction()
    {
        $fileName   = 'statetax.xml';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_statetax_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * mass State change - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massStateAction()
    {
        $statetaxIds = $this->getRequest()->getParam('statetax');
        if (!is_array($statetaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select statetaxs.')
            );
        } else {
            try {
                foreach ($statetaxIds as $statetaxId) {
                    $statetax = Mage::getSingleton('metizsoft_taxgenerate/statetax')->load($statetaxId)
                        ->setState($this->getRequest()->getParam('flag_state'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d statetaxs were successfully updated.', count($statetaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating statetaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass County change - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massCountyAction()
    {
        $statetaxIds = $this->getRequest()->getParam('statetax');
        if (!is_array($statetaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select statetaxs.')
            );
        } else {
            try {
                foreach ($statetaxIds as $statetaxId) {
                $statetax = Mage::getSingleton('metizsoft_taxgenerate/statetax')->load($statetaxId)
                    ->setCounty($this->getRequest()->getParam('flag_county'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d statetaxs were successfully updated.', count($statetaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating statetaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass City change - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massCityAction()
    {
        $statetaxIds = $this->getRequest()->getParam('statetax');
        if (!is_array($statetaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select statetaxs.')
            );
        } else {
            try {
                foreach ($statetaxIds as $statetaxId) {
                $statetax = Mage::getSingleton('metizsoft_taxgenerate/statetax')->load($statetaxId)
                    ->setCity($this->getRequest()->getParam('flag_city'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d statetaxs were successfully updated.', count($statetaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating statetaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }


    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Metizsoft Solutions
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('metizsoft_taxgenerate/statetax');
    }
}
