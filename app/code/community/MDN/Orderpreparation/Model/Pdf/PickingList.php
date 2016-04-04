<?php

/**
 * Generate PDF for picking list
 *
 */
class MDN_Orderpreparation_Model_Pdf_PickingList extends MDN_Orderpreparation_Model_Pdf_Pdfhelper {


    CONST X_POS_IMAGE = 10;
    CONST X_POS_QTY = 55;
    CONST X_POS_NAME = 110;
    CONST X_POS_BARCODE = 420;
    CONST X_POS_LOCATION = 520;

    CONST WIDTH_IMAGE = 40;
    CONST WIDTH_NAME = 230;
    CONST WIDTH_BARCODE = 80;

    /**
     * Enter description here...
     *
     * @param array $data :
     * ---> key comments contains comments
     * ---> key products contains an array with products
     * -------> each product as data : type_id, picture_path, qty, manufacturer, sku, name, location, barcode
     *
     * @return unknown
     */
    public function getPdf($data = array()) {

        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        //init datas
        $comments = $data['comments'];
        $products = $data['products'];

        //init pdf object
        if ($this->pdf == null)
            $this->pdf = new Zend_Pdf();


        //create new page
        $titre = mage::helper('purchase')->__('Picking List');
        $settings = array();
        $settings['title'] = $titre;
        $settings['store_id'] = 0;
        $page = $this->NewPage($settings);
        $this->defineFont($page,10,self::FONT_MODE_BOLD);

        //display comments
        if ($comments) {
            $this->y -=20;
            $offset = $this->DrawMultilineText($page, $comments, 25, $this->y, 12, 0, 18);
            $this->y -= $offset + 10;
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
            $this->y -=10;
        }

        //display table header
        $this->drawTableHeader($page);
        $this->y -=10;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
        $this->defineFont($page,10);

        foreach ($products as $product) {

            //add product picture
            if ($product['picture_path']) {
                if (file_exists($product['picture_path'])) {
                    try {
                        $zendPicture = Zend_Pdf_Image::imageWithPath($product['picture_path']);
                        $page->drawImage($zendPicture, self::X_POS_IMAGE, $this->y - 15, self::WIDTH_IMAGE, $this->y - 15 + 30);
                    } catch (Exception $ex) {
                         mage::logException($ex);
                    }
                }
            }

            //qty
            $this->defineFont($page,10);
            $page->drawText($product['qty'], self::X_POS_QTY, $this->y - 5, 'UTF-8');

            //add product information
            $manufacturerText = $product['manufacturer'];
            if ($manufacturerText)
                $caption = $manufacturerText . ' - ' . $product['sku'];
            else
                $caption = $product['sku'];
            $caption .= "\n" . $product['name'];

            $caption .= mage::helper('AdvancedStock/Product_ConfigurableAttributes')->getDescription($product->getId());
            $caption = $this->WrapTextToWidth($page, $caption, self::WIDTH_NAME);
            $offset = $this->DrawMultilineText($page, $caption, self::X_POS_NAME, $this->y + 5, 12, 0.2, 16);

            //add barcode picture and location
            if ($product['barcode']) {
                try{
                    $picture = mage::helper('AdvancedStock/Product_Barcode')->getBarcodePicture($product['barcode']);
                    if ($picture) {
                        $zendPicture = $this->pngToZendImage($picture);
                        $page->drawImage($zendPicture, self::X_POS_BARCODE, $this->y - 15, self::X_POS_BARCODE + self::WIDTH_BARCODE, $this->y - 15 + 30);
                    }
                }catch(Exception $ex){
                    mage::logException($ex);
                }
            }

            //Draw location
            $page->drawText($product['location'], self::X_POS_LOCATION, $this->y, 'UTF-8');

            if ($offset < 20)
                $offset = 20;
            $this->y -= $offset;

            //line separation
            $page->setLineWidth(0.5);
            $page->drawLine(10, $this->y - 4, $this->_BLOC_ENTETE_LARGEUR, $this->y - 4);
            $this->y -= $this->_ITEM_HEIGHT;

            //new page (if needed)
            if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                $this->drawFooter($page);
                $page = $this->NewPage($settings);
                $this->drawTableHeader($page);
                $this->y -= 20;
            }
        }

        //draw footer
        $this->drawFooter($page);

        //draw pager
        $this->AddPagination($this->pdf);

        $this->_afterGetPdf();

        return $this->pdf;
    }

    /**
     * Table header
     *
     * @param unknown_type $page
     */
    public function drawTableHeader(&$page) {

        $this->y -= 15;
        $this->defineFont($page,12);

        $page->drawText(mage::helper('purchase')->__('Qty'), self::X_POS_QTY, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Product'), self::X_POS_NAME, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Barcode'), self::X_POS_BARCODE, $this->y, 'UTF-8');
        $page->drawText(mage::helper('purchase')->__('Location'), self::X_POS_LOCATION, $this->y, 'UTF-8');

        $this->y -= 8;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $this->y -= 15;
    }

}

