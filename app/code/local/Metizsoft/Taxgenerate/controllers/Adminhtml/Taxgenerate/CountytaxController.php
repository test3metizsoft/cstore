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
 * Countytax admin controller
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Adminhtml_Taxgenerate_CountytaxController extends Metizsoft_Taxgenerate_Controller_Adminhtml_Taxgenerate
{
    /**
     * init the countytax
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Model_Countytax
     */
    protected function _initCountytax()
    {
        $countytaxId  = (int) $this->getRequest()->getParam('id');
        $countytax    = Mage::getModel('metizsoft_taxgenerate/countytax');
        if ($countytaxId) {
            $countytax->load($countytaxId);
        }
        Mage::register('current_countytax', $countytax);
        return $countytax;
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
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Countytaxes'));
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
     * edit countytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function editAction()
    {
        $countytaxId    = $this->getRequest()->getParam('id');
        $countytax      = $this->_initCountytax();
        //echo '<pre>';print_r($countytax);exit;
        if ($countytaxId && !$countytax->getId()) {
            $this->_getSession()->addError(
                Mage::helper('metizsoft_taxgenerate')->__('This countytax no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCountytaxData(true);
        if (!empty($data)) {
            $countytax->setData($data);
        }
        Mage::register('countytax_data', countytax);
        $this->loadLayout();
        $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Tax Generate'))
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Countytaxes'));
        if ($countytax->getId()) {
            $this->_title($countytax->getName());
        } else {
            $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Add countytax'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new countytax action
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
     * save countytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('countytax')) {
            try {
                $countytax = $this->_initCountytax();
                $countytax->addData($data);
                $countytax->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Countytax was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $countytax->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCountytaxData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was a problem saving the countytax.')
                );
                Mage::getSingleton('adminhtml/session')->setCountytaxData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Unable to find countytax to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete countytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $countytax = Mage::getModel('metizsoft_taxgenerate/countytax');
                $countytax->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Countytax was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting countytax.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Could not find countytax to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete countytax - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massDeleteAction()
    {
        $countytaxIds = $this->getRequest()->getParam('countytax');
        if (!is_array($countytaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select countytaxs to delete.')
            );
        } else {
            try {
                foreach ($countytaxIds as $countytaxId) {
                    $countytax = Mage::getModel('metizsoft_taxgenerate/countytax');
                    $countytax->setId($countytaxId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Total of %d countytaxs were successfully deleted.', count($countytaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting countytaxs.')
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
        $countytaxIds = $this->getRequest()->getParam('countytax');
        if (!is_array($countytaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select countytaxs.')
            );
        } else {
            try {
                foreach ($countytaxIds as $countytaxId) {
                $countytax = Mage::getSingleton('metizsoft_taxgenerate/countytax')->load($countytaxId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d countytaxs were successfully updated.', count($countytaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating countytaxs.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
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
        $countytaxIds = $this->getRequest()->getParam('countytax');
        if (!is_array($countytaxIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select countytaxs.')
            );
        } else {
            try {
                foreach ($countytaxIds as $countytaxId) {
                $countytax = Mage::getSingleton('metizsoft_taxgenerate/countytax')->load($countytaxId)
                    ->setState($this->getRequest()->getParam('flag_state'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d countytaxs were successfully updated.', count($countytaxIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating countytaxs.')
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
                Mage::helper('metizsoft_taxgenerate')->__('File is not selected.')
            );
        } else {
            try {
                $csv = new Varien_File_Csv;
                $csvpath = Mage::getBaseDir() . DS . 'var' . DS . 'import' . DS . $csvfile;
                
                $datas = $csv->getData($csvpath);
                $haverow = isset($datas[0])?$datas[0]:0;
                if($haverow):
                    
                    $checkvalueinarray = $this->checkarray(
                            array('countytax', 'county', 'state', 'category', 'taxtype'), 
                            $haverow);
                
                    if($checkvalueinarray != 1){
                        Mage::getSingleton('adminhtml/county')->addError(
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
                        
                        $counties  = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                                    ->addFieldToFilter(array('name'), array(trim($data['county'])))->getFirstItem();
                        
                        $categoryid = Mage::getModel('catalog/category')->getCollection()
                                ->addFieldToFilter('name', $data['category']);
                        $categoryid = $categoryid->getFirstItem()->getId();
                        if(!$categoryid){
                            $errordata .= $data['county'].' '.$data['state'].' '.$data['category'].' Row Have issue </br>';
                            continue;
                        }
                        
                        if($counties->getId()){
                            
                            $status = (isset($data['status'])?$data['status']:1);
                            $county_id = $counties->getId();
                            $updated_at = (isset($data['updated_at'])?$data['updated_at']:date('Y-m-d H:i:s'));
                            $created_at = (isset($data['created_at'])?$data['created_at']:date('Y-m-d H:i:s'));

                            $tax = Mage::getModel('metizsoft_taxgenerate/countytax')->getCollection()
                                ->addFieldToFilter('county_id', $county_id)
                                ->addFieldToFilter('state', $data['state'])
                                ->addFieldToFilter('category_id', $categoryid)->getFirstItem();

                            if($tax->getData()){
                                $taxdata = $tax->getData();

                                $savetax = Mage::getModel('metizsoft_taxgenerate/countytax')->load($taxdata['entity_id']);
                                $savetax->setCountyId($county_id);
                                $savetax->setState($data['state']);
                                $savetax->setCategoryId($categoryid);
                                $savetax->setTaxtype($data['taxtype']);
                                $savetax->setCountyTax($data['county_tax']);
                                $savetax->setStatus($status);
                                $savetax->setUpdatedAt($updated_at);
                                $savetax->save();
                            }else{
                                $savetax = Mage::getModel('metizsoft_taxgenerate/countytax');
                                $savetax->setCountyId($county_id);
                                $savetax->setState($data['state']);
                                $savetax->setCategoryId($categoryid);
                                $savetax->setTaxtype($data['taxtype']);
                                $savetax->setCountyTax($data['county_tax']);
                                $savetax->setStatus($status);
                                $savetax->setUpdatedAt($updated_at);
                                $savetax->setCreatedAt($created_at);
                                $savetax->save();
                            }
                            $successcount = $successcount+1;
                        } else {
                            $errordata .= $data['state'].' '.$data['county'].' '.$data['city'].' Row Have issue </br>';
                        } 
                    }
                    if($successcount > 0){
                        $this->_getSession()->addSuccess(
                            $this->__('Total of %d countytax were successfully updated.', $successcount)
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
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating County csv.')
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
        $fileName   = 'countytax.csv';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_countytax_grid')
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
        $fileName   = 'countytax.xls';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_countytax_grid')
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
        $fileName   = 'countytax.xml';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_countytax_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('metizsoft_taxgenerate/countytax');
    }
}
