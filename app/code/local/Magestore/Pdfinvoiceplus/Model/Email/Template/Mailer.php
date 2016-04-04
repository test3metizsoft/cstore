<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Email Template Mailer Model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Pdfinvoiceplus_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{

    protected $_afile = array(); //(by dw)    


    //custom (by dw)
    //start
    public function addAttachment($fileContents,$fileName)
    {    	
    	$tmp = array();
    	$tmp['fileContents'] = $fileContents;
    	$tmp['fileName'] = $fileName;
        $this->_afile = $tmp;        
        return $this;
    }
    //end
    
    /**
     * Send all emails from email list
     * @see self::$_emailInfos
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function send()
    {
        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            if(!empty($this->_afile))
            {            	
            	$emailTemplate->setEmAttachments($this->_afile); //(by dw)            
            }			
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                $this->getTemplateId(),
                $this->getSender(),
                $emailInfo->getToEmails(),
                $emailInfo->getToNames(),
                $this->getTemplateParams(),
                $this->getStoreId()
            );
        }
        return $this;
    }
}
