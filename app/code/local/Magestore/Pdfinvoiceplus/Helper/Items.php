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
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Helper_Items extends Mage_Catalog_Helper_Product
{

    public function getTheItemsFromBetwin($src, $start, $end)
    {
       /* Edit By Jack 22/12  */
        $txt = explode($start, $src);   
        $txt2 = explode($end, $txt[1]);
	$explode = explode('<tbody>',$src);
	$explode2 = explode('</tbody>',$explode[1]);
	if($txt2[0])
		return trim($txt2[0]);
	else
		return trim($explode2[0]);
       /* End Edit  */
    }

    /**
     * 
     * @return \SimpleHtmlDoom_SimpleHtmlDoomLoad
     */
    public function getTheSimpleHtmlDom()
    {
        $htmlProcessor = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        return $htmlProcessor;
    }

    /**
     * Process the items html.
     * @param string $tempmplateForHtmlProcess
     * @return string
     */
    public function processHtml($tempmplateForHtmlProcess)
    {
        $htmlProcessor = $this->getTheSimpleHtmlDom()
                ->load($tempmplateForHtmlProcess);

        foreach ($htmlProcessor->find('tr') as $e)
        {

            $numtd = $e->find('td');
            $td = count($numtd);
            if ($td == 1)
            {
                $e->innertext = '';
                $delteTd = true;
            }
            foreach ($htmlProcessor->find('td') as $e)
            {
                $plaintext = $e->innertext;
                if ($plaintext == Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator::THE_START)
                {
                    $e->parent->outertext = '';
                }
                if ($plaintext == Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator::THE_END)
                {
                    $e->parent->outertext = '';
                }
            }
        }
        $htmlProcessorFinish = $htmlProcessor->__toString();
        return $htmlProcessorFinish;
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions($options)
    {
        $result = array();
        if ($options)
        {
            if (isset($options['options']))
            {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options']))
            {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info']))
            {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        /* Will will be able to split in three */
        /* Change by Zeus 04/12 */
        $data = NULL;
        /* End change */
        foreach ($result as $option => $value)
        {
            if(is_array($value['label']) && is_array($value['value']))
            $data .= $value['label'] . ' - ' . $value['value'] . '<br/>';
        }
        if (isset($data))
        {
            $productOptionesLabeled = array(
                'items_product_options' => array(
                    'value' => $data,
                    'label' => Mage::helper('pdfinvoiceplus')->__('Product options')
                )
            );   
        }
        return $productOptionesLabeled;
    }

    public function substrCount($haystack, $needle)
    {
        if (isset($haystack) && isset($needle))
        {
            return substr_count($haystack, $needle);
        }
        return false;
    }
}

