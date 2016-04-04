<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Orderpreparation_Helper_Comments extends Mage_Core_Helper_Abstract {

    /**
     * Return all comments possible for given order
     * 
     * Return a string for display
     *
     * @param type $orderId
     */
    public function getAll($order){
        $comments = '' ;

        //organizer of this order
        if (Mage::getStoreConfig('orderpreparation/download_document_options/print_organiser_comments') == 1) {
            $comments .= (string) mage::helper('Organizer')->getEntityCommentsSummary('order', $order->getorder_id(), false);
        }

        //order comments

        $orderComments = $order->getAllStatusHistory();
        $nbComment = count($orderComments);
        if($nbComment>1){
            foreach ($orderComments as $historyItem){
                $comment = $historyItem->getData('comment');
                if($comment){

                    if (Mage::getStoreConfig('orderpreparation/download_document_options/print_order_public_comments') == 1) {
                        if ($historyItem->getData('is_visible_on_front')){
                            $comments .= mage::helper('core')->formatDate($historyItem->getData('created_at'), 'medium').' - '.$comment."\n";
                        }
                    }

                    if (Mage::getStoreConfig('orderpreparation/download_document_options/print_order_private_comments') == 1) {
                        if (!$historyItem->getData('is_visible_on_front')){
                            $comments .= mage::helper('core')->formatDate($historyItem->getData('created_at'), 'medium').' - '.$comment."\n";
                        }
                    }
                }
            }
        }

        return  $comments;

    }
    
}

