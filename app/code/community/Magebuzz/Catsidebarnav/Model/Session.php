<?php
/**
 * @author Magebuzz <support@magebuzz.com>
 */
class Magebuzz_Catsidebarnav_Model_Session extends Mage_Core_Model_Abstract
{
  public function _construct()
  {
    parent::_construct();
    $this->_init('catsidebarnav');
  }
}