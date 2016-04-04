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
 * Citylist admin controller
 *
 * @category    Metizsoft
 * @package     Metizsoft_Taxgenerate
 * @author      Metizsoft Solutions<http://metizsoft.com>
 */
class Metizsoft_Taxgenerate_Adminhtml_Taxgenerate_CitylistController extends Metizsoft_Taxgenerate_Controller_Adminhtml_Taxgenerate
{
    /**
     * init the citylist
     *
     * @access protected
     * @return Metizsoft_Taxgenerate_Model_Citylist
     */
    protected function _initCitylist()
    {
        $citylistId  = (int) $this->getRequest()->getParam('id');
        $citylist    = Mage::getModel('metizsoft_taxgenerate/citylist');
        if ($citylistId) {
            $citylist->load($citylistId);
        }
        Mage::register('current_citylist', $citylist);
        return $citylist;
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
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Citylists'));
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
     * edit citylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function editAction()
    {
        $citylistId    = $this->getRequest()->getParam('id');
        $citylist      = $this->_initCitylist();
        if ($citylistId && !$citylist->getId()) {
            $this->_getSession()->addError(
                Mage::helper('metizsoft_taxgenerate')->__('This citylist no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCitylistData(true);
        if (!empty($data)) {
            $citylist->setData($data);
        }
        Mage::register('citylist_data', $citylist);
        $this->loadLayout();
        $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Tax Generate'))
             ->_title(Mage::helper('metizsoft_taxgenerate')->__('Citylists'));
        if ($citylist->getId()) {
            $this->_title($citylist->getCity());
        } else {
            $this->_title(Mage::helper('metizsoft_taxgenerate')->__('Add citylist'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new citylist action
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
     * save citylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('citylist')) {
            try {
                $citylist = $this->_initCitylist();
                $citylist->addData($data);
                $citylist->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Citylist was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $citylist->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCitylistData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was a problem saving the citylist.')
                );
                Mage::getSingleton('adminhtml/session')->setCitylistData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Unable to find citylist to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete citylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $citylist = Mage::getModel('metizsoft_taxgenerate/citylist');
                $citylist->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Citylist was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting citylist.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('metizsoft_taxgenerate')->__('Could not find citylist to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete citylist - action
     *
     * @access public
     * @return void
     * @author Metizsoft Solutions
     */
    public function massDeleteAction()
    {
        $citylistIds = $this->getRequest()->getParam('citylist');
        if (!is_array($citylistIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select citylists to delete.')
            );
        } else {
            try {
                foreach ($citylistIds as $citylistId) {
                    $citylist = Mage::getModel('metizsoft_taxgenerate/citylist');
                    $citylist->setId($citylistId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('metizsoft_taxgenerate')->__('Total of %d citylists were successfully deleted.', count($citylistIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error deleting citylists.')
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
        $citylistIds = $this->getRequest()->getParam('citylist');
        if (!is_array($citylistIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select citylists.')
            );
        } else {
            try {
                foreach ($citylistIds as $citylistId) {
                $citylist = Mage::getSingleton('metizsoft_taxgenerate/citylist')->load($citylistId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d citylists were successfully updated.', count($citylistIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating citylists.')
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
        $citylistIds = $this->getRequest()->getParam('citylist');
        if (!is_array($citylistIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('metizsoft_taxgenerate')->__('Please select citylists.')
            );
        } else {
            try {
                foreach ($citylistIds as $citylistId) {
                $citylist = Mage::getSingleton('metizsoft_taxgenerate/citylist')->load($citylistId)
                    ->setCounty($this->getRequest()->getParam('flag_county'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d citylists were successfully updated.', count($citylistIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('metizsoft_taxgenerate')->__('There was an error updating citylists.')
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
                Mage::helper('metizsoft_taxgenerate')->__('Please select CsvFile.')
            );
        } else {
            try {
                $csv = new Varien_File_Csv;
                $csvpath = Mage::getBaseDir() . DS . 'var' . DS . 'import' . DS . $csvfile;
                
                $datas = $csv->getData($csvpath);
                $haverow = isset($datas[0])?$datas[0]:0;
                if($haverow):
                    
                    $checkvalueinarray = $this->checkarray(
                            array('county_id', 'city'), 
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
                        $tax = Mage::getModel('metizsoft_taxgenerate/citylist')->getCollection()
                            ->addFieldToFilter('county_id', $data['county_id'])
                            ->addFieldToFilter('city', $data['city'])->getFirstItem();
                        
                        $status = (isset($data['status'])?$data['status']:1);
                        $updated_at = (isset($data['updated_at'])?$data['updated_at']:date('Y-m-d H:i:s'));
                        $created_at = (isset($data['created_at'])?$data['created_at']:date('Y-m-d H:i:s'));
                            
                        if($tax->getData()){
                            $taxdata = $tax->getData();
                            
                            $savetax = Mage::getModel('metizsoft_taxgenerate/citylist')->load($taxdata['entity_id']);
                            $savetax->setCountyId($data['county_id']);
                            $savetax->setCity($data['city']);
                            $savetax->setStatus($status);
                            $savetax->setUpdatedAt($updated_at);
                            $savetax->save();
                        }else{
                            $savetax = Mage::getModel('metizsoft_taxgenerate/citylist');
                            $savetax->setCountyId($data['county_id']);
                            $savetax->setCity($data['city']);
                            $savetax->setStatus($status);
                            $savetax->setUpdatedAt($updated_at);
                            $savetax->setCreatedAt($created_at);
                            $savetax->save();
                        }
                        
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d Citylist were successfully updated.', count($csvdata))
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
        $fileName   = 'citylist.csv';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_citylist_grid')
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
        $fileName   = 'citylist.xls';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_citylist_grid')
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
        $fileName   = 'citylist.xml';
        $content    = $this->getLayout()->createBlock('metizsoft_taxgenerate/adminhtml_citylist_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('metizsoft_taxgenerate/citylist');
    }
}
