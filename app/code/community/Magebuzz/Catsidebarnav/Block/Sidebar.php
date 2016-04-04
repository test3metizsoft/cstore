<?php
/**
 * @author Magebuzz <support@magebuzz.com>
 */
class Magebuzz_Catsidebarnav_Block_Sidebar extends Magebuzz_Catsidebarnav_Block_Catsidebarnav
{
  public function _construct() {
    $this->setTemplate('catsidebarnav/catsidebarnav.phtml');
    return parent::_construct();
  }
  public function _prepareLayout() {
    if (Mage::helper('catsidebarnav')->isEnabled()) {
      $headBlock = $this->getLayout()->getBlock('head');
      if ($headBlock) {
        $type = Mage::helper('catsidebarnav')->getShowType();
        switch($type) {
          case "static":
            $headBlock->addCss('css/magebuzz/catsidebarnav/static.css');
            break;
          case "click-2-click":
            $headBlock->addCss('css/magebuzz/catsidebarnav/click.css');
            $headBlock->addItem('skin_js','js/magebuzz/catsidebarnav/click2click.js');
            break;
          case "fly-out":
            $headBlock->addCss('css/magebuzz/catsidebarnav/fly-out.css');
            $headBlock->addItem('skin_js','js/magebuzz/catsidebarnav/fly-out/hoverIntent.js');
            $headBlock->addItem('skin_js','js/magebuzz/catsidebarnav/fly-out/superfish.js');
            $headBlock->addItem('skin_js','js/magebuzz/catsidebarnav/fly-out/fly-out.js');
            break;
          default:
            $headBlock->addCss('css/magebuzz/catsidebarnav/static.css');
            break;
        }
      }
    }
    return parent::_prepareLayout();
  }
  public function getStoreCategories() {
    $helper = Mage::helper('catsidebarnav/category');
    return $helper->getAllCategories();
  }
  protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,$isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false) {
    $showType=Mage::getStoreConfig('catsidebarnav/display_settings/show_type');
    if (!$category->getIsActive()) {
      return '';
    }
    $html = array();

    // get all children
    if (Mage::helper('catalog/category_flat')->isEnabled()) {
      $children = (array)$category->getChildrenNodes();
      $childrenCount = count($children);
    } else {
      $children = $category->getChildren();
      $childrenCount = $children->count();
    }
    $hasChildren = ($children && $childrenCount);

    // select active children
    $activeChildren = array();
    foreach ($children as $child) {
      if ($child->getIsActive()) {
        $activeChildren[] = $child;
      }
    }
    $activeChildrenCount = count($activeChildren);
    $hasActiveChildren = ($activeChildrenCount > 0);

    // prepare list item html classes
    $classes = array();
    $classes[] = 'level' . $level;
    $classes[] = 'nav-' . $this->_getItemPosition($level);
    if ($this->isCategoryActive($category)) {
      $classes[] = 'active';
    }
    $linkClass = '';
    if ($isOutermost && $outermostItemClass) {
      $classes[] = $outermostItemClass;
      $linkClass = ' class="'.$outermostItemClass.'"';
    }
    if ($isFirst) {
      $classes[] = 'first';
    }
    if ($isLast) {
      $classes[] = 'last';
    }
    if ($hasActiveChildren) {
      $classes[] = 'parent';
    }

    // prepare list item attributes
    $attributes = array();
    if (count($classes) > 0) {
      $attributes['class'] = implode(' ', $classes);
    }
    if ($hasActiveChildren && !$noEventAttributes) {
       $attributes['onmouseover'] = 'toggleMenu(this,1)';
       $attributes['onmouseout'] = 'toggleMenu(this,0)';
    }

    // assemble list item with attributes
    $htmlLi = '<li';
    foreach ($attributes as $attrName => $attrValue) {
      $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
    }
    $htmlLi .= '>';
    $html[] = $htmlLi;
    if ($hasActiveChildren) {
      if($showType == 'click-2-click'){
        $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';
        $html[] = '<a href="javascript://" class="right show-cat">&nbsp;';
        $html[] = '</a>';
      }
      else{
        $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';
      }
    }
    else{
      $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
      $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
      $html[] = '</a>';
    }
    // render children
    $htmlChildren = '';
    $j = 0;
    foreach ($activeChildren as $child) {
      $htmlChildren .= $this->_renderCategoryMenuItemHtml(
        $child,
        ($level + 1),
        ($j == $activeChildrenCount - 1),
        ($j == 0),
        false,
        $outermostItemClass,
        $childrenWrapClass,
        $noEventAttributes
      );
      $j++;
    }
    if (!empty($htmlChildren)) {
      if ($childrenWrapClass) {
        $html[] = '<div class="' . $childrenWrapClass . '">';
      }
      $html[] = '<ul class="level' . $level . '">';
      $html[] = $htmlChildren;
      $html[] = '</ul>';
      if ($childrenWrapClass) {
        $html[] = '</div>';
      }
    }

    $html[] = '</li>';

    $html = implode("\n", $html);
    return $html;
  }
  
  public function addCssJsHead() {
    return $this;
  }
}