<?php
class Metizsoft_Reports_Model_Mysql4_Reports extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("metizsoft_report/report", "reports_id");
    }
}