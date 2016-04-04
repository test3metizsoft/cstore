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
 * Countylist admin controller
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Adminhtml_Taxgenerate_CountylistController extends Metizsoft_Taxgenerate_Controller_Adminhtml_Taxgenerate
{
    /**
     * init the countylist
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Model_Countylist
     */
    protected function _initCountylist()
    {
        $countylistId  = (int) $this->getRequest()->getParam('id');
        $countylist    = Mage::getModel('metizsoft_taxgenerate/countylist');
        if ($countylistId) {
            $countylist->load($countylistId);
        }
        Mage::register('current_countylist', $countylist);
        return $countylist;
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
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Countylists'));
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
     * edit countylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function editAction()
    {
        $countylistId    = $this->getRequest()->getParam('id');
        $countylist      = $this->_initCountylist();
        if ($countylistId && !$countylist->getId()) {
            $this->_getSession()->addError(
                Mage::helper('metizsoft_taxgenerate')->__('This countylist no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCountylistData(true);
        if (!empty($data)) {
            $countylist->setData($data);
        }
        Mage::register('countylist_data', $countylist);
        $this->loadLayout();
        $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Tax Generate'))
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Countylists'));
        if ($countylist->getId()) {
            $this->_title($countylist->getName());
        } else {
            $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Add countylist'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new countylist action
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
     * save countylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('countylist')) {
            try {
                $countylist = $this->_initCountylist();
                $countylist->addData($data);
                $countylist->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Countylist was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $countylist->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCountylistData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was a problem saving the countylist.')
                );
                Mage::getSingleton('adminhtml/session')->setCountylistData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Unable to find countylist to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete countylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $countylist = Mage::getModel('metizsoft_taxgenerate/countylist');
                $countylist->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Countylist was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting countylist.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Could not find countylist to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete countylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massDeleteAction()
    {
        $countylistIds = $this->getRequest()->getParam('countylist');
        if (!is_array($countylistIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select countylists to delete.')
            );
        } else {
            try {
                foreach ($countylistIds as $countylistId) {
                    $countylist = Mage::getModel('metizsoft_taxgenerate/countylist');
                    $countylist->setId($countylistId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Total of %d countylists were successfully deleted.', count($countylistIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting countylists.')
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
        $countylistIds = $this->getRequest()->getParam('countylist');
        if (!is_array($countylistIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select countylists.')
            );
        } else {
            try {
                foreach ($countylistIds as $countylistId) {
                $countylist = Mage::getSingleton('metizsoft_taxgenerate/countylist')->load($countylistId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d countylists were successfully updated.', count($countylistIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating countylists.')
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
        $countylistIds = $this->getRequest()->getParam('countylist');
        if (!is_array($countylistIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select countylists.')
            );
        } else {
            try {
                foreach ($countylistIds as $countylistId) {
                $countylist = Mage::getSingleton('metizsoft_taxgenerate/countylist')->load($countylistId)
                    ->setState($this->getRequest()->getParam('flag_state'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d countylists were successfully updated.', count($countylistIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating countylists.')
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
                Mage::helper('metizsoft_taxgenerate')->__('Please select Countylist.')
            );
        } else {
            try {
                $csv = new Varien_File_Csv;
                $csvpath = Mage::getBaseDir() . DS . 'var' . DS . 'import' . DS . $csvfile;
                
                $datas = $csv->getData($csvpath);
                $haverow = isset($datas[0])?$datas[0]:0;
                if($haverow):
                    
                    $checkvalueinarray = $this->checkarray(
                            array('state', 'countyname'),
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
                    //echo '<pre>';print_r($csvdata);exit;
                    foreach ($csvdata as $data) {
                        $tax = Mage::getModel('metizsoft_taxgenerate/countylist')->getCollection()
                            ->addFieldToFilter('name', $data['countyname'])
                            ->addFieldToFilter('state', $data['state'])->getFirstItem();
                        
                        $status = (isset($data['status'])?$data['status']:1);
                        $updated_at = (isset($data['updated_at'])?$data['updated_at']:date('Y-m-d H:i:s'));
                        $created_at = (isset($data['created_at'])?$data['created_at']:date('Y-m-d H:i:s'));
                            
                        if($tax->getData()){
                            $taxdata = $tax->getData();
                            
                            $savetax = Mage::getModel('metizsoft_taxgenerate/countylist')->load($taxdata['entity_id']);
                            $savetax->setName($data['countyname']);
                            $savetax->setState($data['state']);
                            $savetax->setStatus($status);
                            $savetax->setUpdatedAt($updated_at);
                            $savetax->save();
                        }else{
                            $savetax = Mage::getModel('metizsoft_taxgenerate/countylist');
                            $savetax->setName($data['countyname']);
                            $savetax->setState($data['state']);
                            $savetax->setStatus($status);
                            $savetax->setUpdatedAt($updated_at);
                            $savetax->setCreatedAt($created_at);
                            $savetax->save();
                        }
                        
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d Countylist were successfully updated.', count($csvdata))
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
        $fileName   = 'countylist.csv';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_countylist_grid')
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
        $fileName   = 'countylist.xls';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_countylist_grid')
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
        $fileName   = 'countylist.xml';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_countylist_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('metizsoft_taxgenerate/countylist');
    }
}
