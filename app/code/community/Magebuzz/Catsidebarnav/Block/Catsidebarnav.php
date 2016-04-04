<?php
/**
 * @author Magebuzz <support@magebuzz.com>
 */
class Magebuzz_Catsidebarnav_Block_Catsidebarnav extends Mage_Catalog_Block_Navigation
{
  public function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getCatsidebarnav() {
    if (!$this->hasData('catsidebarnav')) {
      $this->setData('catsidebarnav', Mage::registry('catsidebarnav'));
    }
    return $this->getData('catsidebarnav');
  }
  public function leftSidebarBlock() {
    if (Mage::helper('catsidebarnav')->isEnabled()) {
      $block = $this->getParentBlock();
      if($block) {
        if(Mage::helper('catsidebarnav')->displayOnSideBar() == 'left') {
          $sidebarBlock = $this->getLayout()->createBlock('catsidebarnav/sidebar');
          $block->insert($sidebarBlock,'', false, 'cat-sidebar');
        }
      }
    }
  }
  public function rightSidebarBlock() {
    if (Mage::helper('catsidebarnav')->isEnabled()) {
      $block = $this->getParentBlock();
      if($block) {
        if(Mage::helper('catsidebarnav')->displayOnSideBar() == 'right') {
          $sidebarBlock = $this->getLayout()->createBlock('catsidebarnav/sidebar');

          $block->insert($sidebarBlock, '', false, 'cat-sidebar');
        }
      }
    }
  }
}