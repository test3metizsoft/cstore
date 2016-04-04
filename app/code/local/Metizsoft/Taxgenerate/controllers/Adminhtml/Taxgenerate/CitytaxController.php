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
 * Citytax admin controller
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Adminhtml_Taxgenerate_CitytaxController extends Metizsoft_Taxgenerate_Controller_Adminhtml_Taxgenerate
{
    /**
     * init the citytax
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Model_Citytax
     */
    protected function _initCitytax()
    {
        $citytaxId  = (int) $this->getRequest()->getParam('id');
        $citytax    = Mage::getModel('metizsoft_taxgenerate/citytax');
        if ($citytaxId) {
            $citytax->load($citytaxId);
        }
        Mage::register('current_citytax', $citytax);
        return $citytax;
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
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Citytaxs'));
        $this->renderLayout();
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
     * edit citytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function editAction()
    {
        $citytaxId    = $this->getRequest()->getParam('id');
        $citytax      = $this->_initCitytax();
        if ($citytaxId && !$citytax->getId()) {
            $this->_getSession()->addError(
                Mage::helper('metizsoft_taxgenerate')->__('This citytax no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCitytaxData(true);
        if (!empty($data)) {
            $citytax->setData($data);
        }
        Mage::register('citytax_data', $citytax);
        $this->loadLayout();
        $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Tax Generate'))
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Citytax'));
        if ($citytax->getId()) {
            $this->_title($citytax->getCity());
        } else {
            $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Add citytax'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new citytax action
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
     * save citytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('citytax')) {
            try {
                $citytax = $this->_initCitytax();
                $citytax->addData($data);
                $citytax->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Citytax was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $citytax->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCitytaxData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was a problem saving the citytax.')
                );
                Mage::getSingleton('adminhtml/session')->setCitytaxData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Unable to find citytax to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete citytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $citytax = Mage::getModel('metizsoft_taxgenerate/citytax');
                $citytax->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Citytax was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting citytax.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Could not find citytax to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete citytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massDeleteAction()
    {
        $citytaxIds = $this->getRequest()->getParam('citytax');
        if (!is_array($citytaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select citytaxs to delete.')
            );
        } else {
            try {
                foreach ($citytaxIds as $citytaxId) {
                    $citytax = Mage::getModel('metizsoft_taxgenerate/citytax');
                    $citytax->setId($citytaxId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Total of %d citytaxs were successfully deleted.', count($citytaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting citytaxs.')
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
        $citytaxIds = $this->getRequest()->getParam('citytax');
        if (!is_array($citytaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select citytaxs.')
            );
        } else {
            try {
                foreach ($citytaxIds as $citytaxId) {
                $citytax = Mage::getSingleton('metizsoft_taxgenerate/citytax')->load($citytaxId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d citytaxs were successfully updated.', count($citytaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating citytaxs.')
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
        $citytaxIds = $this->getRequest()->getParam('citytax');
        if (!is_array($citytaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select citytaxs.')
            );
        } else {
            try {
                foreach ($citytaxIds as $citytaxId) {
                $citytax = Mage::getSingleton('metizsoft_taxgenerate/citytax')->load($citytaxId)
                    ->setCounty($this->getRequest()->getParam('flag_county'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d citytaxs were successfully updated.', count($citytaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating citytaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    
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
                Mage::helper('metizsoft_taxgenerate')->__('Please select Csv files.')
            );
        } else {
            try {
                $csv = new Varien_File_Csv;
                $csvpath = Mage::getBaseDir() . DS . 'var' . DS . 'import' . DS . $csvfile;
                
                $datas = $csv->getData($csvpath);
                $haverow = isset($datas[0])?$datas[0]:0;
                if($haverow):
                    
                    $checkvalueinarray = $this->checkarray(
                            array('citytax', 'state', 'county', 'city', 'category', 'taxtype'), 
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
                    
                    $errordata = '';
                    $successcount = 0;
                    
                    foreach ($csvdata as $data) {
                        
                        $county  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                                    ->addFieldToFilter(array('name'), array(trim($data['county'])))->getFirstItem();
                        
                        $city  = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                                    ->addFieldToFilter(array('city'), array(trim($data['city'])))
                                    ->addFieldToFilter(array('county_id'), array($county->getId()))->getFirstItem();
                        
                        $categoryid = Mage::getModel('catalog/category')->getCollection()
                                ->addFieldToFilter('name', $data['category']);
                        $categoryid = $categoryid->getFirstItem()->getId();
                        if(!$categoryid){
                            $errordata .= $data['state'].' '.$data['county'].' '.$data['city'].' '.$data['category'].' Row Have issue </br>';
                            continue;
                        }
                        
                        if($county->getId() && $city->getId()){
                            
                            $tax = Mage::getModel('metizsoft_taxgenerate/citytax')->getCollection()
                                ->addFieldToFilter('state', $data['state'])
                                ->addFieldToFilter('county_id', $county->getId())
                                ->addFieldToFilter('category_id', $categoryid)
                                ->addFieldToFilter('city_id', $city->getId())->getFirstItem();

                            $status = (isset($data['status'])?$data['status']:1);
                            $updated_at = (isset($data['updated_at'])?$data['updated_at']:date('Y-m-d H:i:s'));
                            $created_at = (isset($data['created_at'])?$data['created_at']:date('Y-m-d H:i:s'));

                            if($tax->getData()){
                                $taxdata = $tax->getData();

                                $savetax = Mage::getModel('metizsoft_taxgenerate/citytax')->load($taxdata['entity_id']);
                                $savetax->setState($data['state']);
                                $savetax->setCountyId($county->getId());
                                $savetax->setCityId($city->getId());
                                $savetax->setCategoryId($categoryid);
                                $savetax->setTaxtype($data['taxtype']);
                                $savetax->setCityTax($data['citytax']);
                                $savetax->setStatus($status);
                                $savetax->setUpdatedAt($updated_at);
                                $savetax->save();
                            }else{
                                $savetax = Mage::getModel('metizsoft_taxgenerate/citytax');
                                $savetax->setState($data['state']);
                                $savetax->setCountyId($county->getId());
                                $savetax->setCityId($city->getId());
                                $savetax->setCategoryId($categoryid);
                                $savetax->setTaxtype($data['taxtype']);
                                $savetax->setCityTax($data['citytax']);
                                $savetax->setStatus($status);
                                $savetax->setUpdatedAt($updated_at);
                                $savetax->setCreatedAt($created_at);
                                $savetax->save();
                            }
                            $successcount = $successcount+1;
                        } else {
                            $errordata .= $data['state'].' '.$data['county'].' '.$data['city'].' '.$data['category'].' Row Have issue </br>';
                        }
                        
                    }
                    if($successcount > 0){
                        $this->_getSession()->addSuccess(
                            $this->__('Total of %d Citytax were successfully updated.', $successcount)
                        );
                    }
                    
                    if($errordata != ''){
                        $this->_getSession()->addNotice(
                            $this->__($errordata)
                        );
                    }
                    
                endif;
                
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating Citytax.')
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
        $fileName   = 'citytax.csv';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_citytax_grid')
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
        $fileName   = 'citytax.xls';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_citytax_grid')
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
        $fileName   = 'citytax.xml';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_citytax_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
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
        return Mage::getSingleton('admin/session')->isAllowed('metizsoft_taxgenerate/citytax');
    }
}
