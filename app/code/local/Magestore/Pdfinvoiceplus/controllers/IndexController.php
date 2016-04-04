<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Pdfinvoiceplus Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();   //phuong
    }
    public function uploadlogoAction(){
        $filename = '';
        if (isset($_FILES['insert-logo']['name']) && $_FILES['insert-logo']['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader('insert-logo');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(false);

                // Set the file upload mode 
                // false -> get the file directly in the specified folder
                // true -> get the file in the product like folders 
                //    (file.jpg will go in something like /media/f/i/file.jpg)
                $uploader->setFilesDispersion(true);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'magestore' . DS . 'pdfinvoiceplus'.DS.'logo';
                $result = $uploader->save($path, $_FILES['insert-logo']['name']);
                $filename = $result['file'];
                
                $logo = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/logo/'.$filename;
                Mage::getModel('pdfinvoiceplus/template')->load($this->getRequest()->getParam('id'))
                    ->setCompanyLogo($filename)
                    ->save();
                $this->getResponse()->setBody('<img width="160" src="'.$logo.'" />');
            } catch (Exception $e) {
                $filename = $_FILES['insert-logo']['name'];
            }
        }
    }
    
    public function changebackgroundAction(){
        $filename = '';
        if (isset($_FILES['change-background']['name']) && $_FILES['change-background']['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader('change-background');

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(false);

                // Set the file upload mode 
                // false -> get the file directly in the specified folder
                // true -> get the file in the product like folders 
                //    (file.jpg will go in something like /media/f/i/file.jpg)
                $uploader->setFilesDispersion(false);

                // We set media as the upload dir
                $path = Mage::getBaseDir('media') . DS . 'magestore' . DS . 'pdfinvoiceplus'.DS.'background';
                $result = $uploader->save($path, $_FILES['change-background']['name']);
                $filename = $result['file'];
                $this->getResponse()->setBody(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'magestore/pdfinvoiceplus/background/'.$filename);
            } catch (Exception $e) {
                $filename = $_FILES['change-background']['name'];
            }
        }
    }
    
    public function loadColumnAction(){
        $request = $this->getRequest()->getParams();
        $type = 'order';
        
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        
        if(isset($request['type'])){
            $type = $request['type'];
        }
        if($type == 'invoice'){
            $table_name = $resource->getTableName('sales/invoice');
        }else if($type == 'creditmemo'){
            $table_name = $resource->getTableName('sales/creditmemo');
        }else{//order
            $table_name = $resource->getTableName('sales/order');
        }
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='".$table_name."' "
                                    . "AND COLUMN_NAME not like '%base_%' "
                                    ."AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach($variableList as $var){
            if($var['COLUMN_COMMENT'] != ''){
                $variables[] = array(
                    'value' => "{{var {$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('pdfinvoiceplus')->__("{$var['COLUMN_COMMENT']}")
                );
            }else{
                $variables[] = array(
                    'value' => "{{var {$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('pdfinvoiceplus')->__("{$var['COLUMN_NAME']}")
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
    
    public function loadColumnItemsAction(){
        $request = $this->getRequest()->getParams();
        $type = 'order';
        
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $config = $conn->getConfig();
        $dbName = $config['dbname'];
        if(isset($request['type'])){
            $type = $request['type'];
        }
        if($type == 'invoice'){
            $table_name = $resource->getTableName('sales/invoice_item');
        }else if($type == 'creditmemo'){
            $table_name = $resource->getTableName('sales/creditmemo_item');
        }else{//order
            $table_name = $resource->getTableName('sales/order_item');
        }
        $variableList = $conn->fetchAll("select COLUMN_NAME, COLUMN_COMMENT
                                from INFORMATION_SCHEMA.COLUMNS
                                where TABLE_NAME='".$table_name."' AND COLUMN_NAME not like '%base_%' "
                                    ."AND TABLE_SCHEMA = '{$dbName}'");
        //to option variables
        $variables = array();
        foreach($variableList as $var){
            if($var['COLUMN_COMMENT'] != ''){
                $variables[] = array(
                    'value' => "{{var {$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('pdfinvoiceplus')->__("{$var['COLUMN_COMMENT']}")
                );
            }else{
                $variables[] = array(
                    'value' => "{{var {$var['COLUMN_NAME']}}}",
                    'label' => Mage::helper('pdfinvoiceplus')->__("{$var['COLUMN_NAME']}")
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
    
    public function csvAction(){
        
        $dump = Mage::helper('pdfinvoiceplus/localization')
            //->setLocalization('spain')
            ->translate('Shipping & Handling');
        zend_debug::dump($dump);die;
        
        $p = 'magestore\pdfinvoiceplus\localization\localization_england.csv';
        $path = str_replace(array('\\','/'), DS, $p);
        $file = Mage::getBaseDir('media').DS.$path;
        $csv = new Varien_File_Csv();
        $data = $csv->getDataPairs($file);
        zend_debug::dump($data);die;
//        die($file);
        $csv = scandir($file);
        $data = array();
        foreach($csv as $file){
            if(preg_match('/^localization/', $file)){
                $data[] = $file;
            }
        }
        zend_debug::dump($data);die;
        for($i=1; $i<count($data); $i++)
        {
            var_dump( $data[$i] );
        }
    }
}
