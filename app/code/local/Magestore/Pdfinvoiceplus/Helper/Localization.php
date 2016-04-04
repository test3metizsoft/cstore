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
 * Pdfinvoiceplus Helper
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Tit Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Helper_Localization extends Mage_Core_Helper_Abstract
{
    const _PATH_FOLDER = 'magestore/pdfinvoiceplus/localization';
    const _PRE_FILE_NAME = 'localization';
    const _PRE_CACHE_KEY = 'pdfinvoiceplus_localization_';

    protected $_cache;
    protected $_code;
    protected $_is_nofile = false;
    
    
    public function __construct() {
        $this->_cache = Mage::getSingleton('core/cache');
        if($this->testCode('default')){
            $this->setLocalization('default');
        }else{
            $this->setLocalization('england');
        }
        if(!$this->testCode($this->_code)){
            $list  = $this->getList();
            if(count($list) > 0){
                $this->setLocalization($list[0]['key']);
            }else{
                $this->_is_nofile = true;
            }
        }
    }

    /**
     * get the list of localization
     * @return array()
     */
    public function getList(){
        $list = $this->getAllFileName();
        $options = array();
        foreach ($list as $name){
            $cus_csv_ext = substr($name, 0, -4);
            $arr_temp = explode('_', $cus_csv_ext);
            array_shift($arr_temp);
            $label = '';
            foreach ($arr_temp as $part_name){
                $label .= ' '.$part_name;
            }
            $label = substr($label, 1);
            $label = ucwords(strtolower($label));
            $code = substr($cus_csv_ext,strlen(self::_PRE_FILE_NAME)+1);
            $options[] = array('key' => $code, 'value'=>$label);
        }
        return $options;
    }
    
    /**
     * set locale use
     * @param type $code code of file localization
     * @return Magestore_Pdfinvoiceplus_Helper_Localization
     */
    public function setLocalization($code){
        if($this->testCode($code)){
            $this->_code = $code;
        }
        return $this;
    }
    
    public function currentLocale(){
        return $this->_code;
    }


    /**
     * get stranslate locale
     * @param type $word
     * @return string
     */
    public function translate($word){
        $data_csv = array();
        if($this->_is_nofile){
           return $word; 
        }
        if(Mage::app()->useCache('translate')){
            $data = unserialize($this->_cache->load(self::_PRE_CACHE_KEY.$this->_code));
        }else{
            $data = '';
        }
        if($data == ''){
            $data_csv = $this->readCsvFile($this->_code);
        }else{
            $data_csv = $data;
        }
        /* Change by Zeus  03/12 */
        if(isset($data_csv[strtoupper($word)]))
        $trans = $data_csv[strtoupper($word)];
        
        if(!isset($trans)){
            return $word;
        }
        /* End change */
        // Change By Jack 04/12
        return utf8_encode($trans);
        //End Change
    }
    
    
    /**
     * @return array
     */
    protected function getAllFileName(){
        $path = str_replace(array('\\','/'), DS, self::_PATH_FOLDER);
        $file = Mage::getBaseDir('media').DS.$path;
        $csv = scandir($file);
        $data = array();
        foreach($csv as $file){
            if(preg_match('/^'.self::_PRE_FILE_NAME.'/', $file)){
                $data[] = $file;
            }
        }
        return $data;
    }
    
    /**
     * 
     * @param type $file_code is code of options function getList()
     * @return array
     */
    protected function readCsvFile($file_code){
        $f_name = $this->getFileNameByCode($file_code);
        $path_folder = str_replace(array('\\','/'), DS, self::_PATH_FOLDER);
        $folder = Mage::getBaseDir('media').DS.$path_folder;
        $file = $folder.DS.$f_name;
        $csv = new Varien_File_Csv();
        $data = $this->getDataPairs($csv->getData($file));
        //if use cache
        if(Mage::app()->useCache('translate')){
            $this->_cache->save(serialize($data), self::_PRE_CACHE_KEY.$file_code, array('cache_localization_data'), 60*60*24);
        }
        return $data;
    }
    
    protected function getFileNameByCode($code){
        $file_name = self::_PRE_FILE_NAME.'_'.$code.'.csv';
        return $file_name;
    }
    
    protected function testCode($code){
        $f_name = $this->getFileNameByCode($code);
        $path_folder = str_replace(array('\\','/'), DS, self::_PATH_FOLDER);
        $folder = Mage::getBaseDir('media').DS.$path_folder;
        $file = $folder.DS.$f_name;
        return is_file($file);
    }
    
    /**
     * Retrieve CSV file data as pairs
     *
     * @param   string $file
     * @param   int $keyIndex
     * @param   int $valueIndex
     * @return  array
     */
    protected function getDataPairs($csvData = array(), $keyIndex=0, $valueIndex=1)
    {
        foreach ($csvData as $rowData) {
            if (isset($rowData[$keyIndex])) {
                $data[strtoupper($rowData[$keyIndex])] = isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null;
            }
        }
        return $data;
    }
}