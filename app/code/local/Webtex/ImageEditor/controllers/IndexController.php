<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your 
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_ImageEditor
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */ 
 
class Webtex_ImageEditor_IndexController extends Mage_Core_Controller_Front_Action
{
    public function replaceAction()
    {
        $url = $this->getRequest()->getParam('url');
        $old = $this->getRequest()->getParam('postdata'); 
        $old = substr($old,strpos($old,'/catalog/product'));
        $image_data = file_get_contents($url);
        file_put_contents(Mage::getBaseDir('media') . $old, $image_data);
        Mage::getModel('core/cache')->flush();
    }
    
    public function saveAction()
    {
        $path = Mage::getBaseDir('media') . '/catalog/product';
        $file = $this->getRequest()->getParam('filename');
        $names = explode('/',$file);

        $fileName = $path . $file ;

        $contentLength = filesize($fileName);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Type', 'application/download; name=' . $fileName, true)
            ->setHeader('Content-Length', $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename='.$names[count($names)-1]);
        $this->getResponse()->sendHeaders();
        readfile($fileName);

    }
}
                                             