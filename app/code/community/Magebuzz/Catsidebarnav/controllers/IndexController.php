<?php
/**
 * @author Magebuzz <support@magebuzz.com>
 */
class Magebuzz_Catsidebarnav_IndexController extends Mage_Core_Controller_Front_Action
{
  public function indexAction()
  {
    $this->loadLayout();
    $this->renderLayout();
  }
}