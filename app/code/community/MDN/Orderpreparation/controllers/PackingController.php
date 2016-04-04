<?php

class MDN_Orderpreparation_PackingController extends Mage_Adminhtml_Controller_Action {

    /**
     * Main screen for packing
     */
    public function indexAction() {
        $this->loadLayout();

        $this->_setActiveMenu('erp');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Packing'));

        $this->renderLayout();
    }

    public function OrderInformationAction() {
        
        //init var
        $response = array();
        $response['error'] = false;
        $response['message'] = '';

        $barcode = $this->getRequest()->getParam('barcode');

        try {
            
            //get order
            $order = Mage::getModel('sales/order')->load($barcode, 'increment_id');
            if (!$order->getId())
                throw new Exception($this->__('Unable to find order #%s', $barcode));
            
            //get order to prepare
            $orderToPrepare = Mage::getModel('Orderpreparation/ordertoprepare')->load($order->getId(), 'order_id');
            if (!$orderToPrepare->getId())
            {
                if (Mage::getStoreConfig('orderpreparation/packing/automatically_add_order_in_selected_orders'))
                {
                    Mage::getModel('Orderpreparation/ordertoprepare')->AddSelectedOrder($order->getId());
                    $orderToPrepare = Mage::getModel('Orderpreparation/ordertoprepare')->load($order->getId(), 'order_id');
                    if (!$orderToPrepare->getId())
                       throw new Exception($this->__('This order is not in the selected orders'));
                }
            }
            
            //check that order is not shipped
            if (Mage::getStoreConfig('orderpreparation/packing/prevent_packing_if_order_already_shipped')) {
                if ($orderToPrepare->getshipment_id())
                    throw new Exception($this->__('This order is already packed !'));
            }

            //check that order is invoiced
            if (Mage::getStoreConfig('orderpreparation/packing/prevent_packing_if_no_invoice')) {
                if (!$orderToPrepare->getinvoice_id())
                    throw new Exception($this->__('This order is not invoiced !'));
            }
            
            //return order information
            $block = $this->getLayout()->createBlock('Orderpreparation/Packing_Products');
            $block->setTemplate('Orderpreparation/Packing/Products.phtml');
            $block->setOrder($order);
            $orderInformation = $block->toHtml();
            $response['order_html'] = $orderInformation;
            $response['order_id'] = $order->getId();

            $response['products_json'] = $this->getProductJson($order);
            
            $response['group_ids'] = $this->getProductJson($order, true);
        } catch (Exception $ex) {
            $response['error'] = true;
            $response['message'] = $ex->getMessage();
        }


        //return response
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * return product or groups json array
     * 
     * @param <type> $order
     */
    protected function getProductJson($order, $group = false) {
        $array = array();

        $orderId = $order->getId();
        $products = Mage::getModel('Orderpreparation/ordertoprepare')->GetItemsToShip($orderId);
        foreach ($products as $product) {
            if ($this->productManageStock($product)) {
                $item = array();
                $item['name'] = $product->getSalesOrderItem()->getName();
                $item['id'] = $product->getId();
                $item['qty_scanned'] = 0;
                $item['serials'] = $product->getSalesOrderItem()->getErpOrderItem()->getserials();
                $item['qty'] = $product->getqty();
                $item['barcode'] = Mage::helper('AdvancedStock/Product_Barcode')->getBarcodeForProduct($product->getproduct_id());
                $currentItemGroup = $product->getSalesOrderItem()->getparent_item_id();
                if (!$currentItemGroup)
                    $currentItemGroup = 'simple';
                $item['group_id'] = $currentItemGroup;
                
                //manage additional barcodes
                $productId = $product->getproduct_id();
                $item['additional_barcodes'] = '';
                //add other barcodes
                $barcodes = Mage::helper('AdvancedStock/Product_Barcode')->getBarcodesForProduct($productId);
                foreach($barcodes as $barcode)
                {
                    if ($item['barcode'] != $barcode->getppb_barcode())
                    {
                        $item['additional_barcodes'] .= $barcode->getppb_barcode().',';
                    }
                }                
                
                if ($group == false)
                    $array[] = $item;
                else
                {
                    if (!in_array($currentItemGroup, $array))
                            $array[] = $currentItemGroup;
                }
            }
        }

        return $array;
    }

    /**
     * return true if product manage stocks
     * 
     * @param type $orderToPrepareItem
     * @return type 
     */
    public function productManageStock($orderToPrepareItem) {
        $productId = $orderToPrepareItem->getproduct_id();
        $product = Mage::getModel('catalog/product')->load($productId);
        return $product->getStockItem()->getManageStock();
    }

    /**
     * Commit packing 
     */
    public function CommitAction() {
        $orderId = $this->getRequest()->getPost('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $weight = $this->getRequest()->getPost('weight');
        $parcelCount = $this->getRequest()->getPost('parcel_count');

        try {

            //update weight
            $orderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($orderId, 'order_id');
            $orderToPrepare->setreal_weight($weight)->save();
            $orderToPrepare->setpackage_count($parcelCount)->save();

            //update serials
            $serials = $this->getRequest()->getPost('serials');
            if ($serials)
            {
                $tSerials = explode(';', $serials);
                foreach ($tSerials as $tSerial)
                {
                    //extract get datas
                    $t = explode('=', $tSerial);
                    if (count($t) != 2)
                        continue;
                    list($orderToPrepareItemId, $serialNumbers) = $t;

                    //insert into erp_sales_flat_order_item
                    $orderToPrepareItem = Mage::getModel('Orderpreparation/ordertoprepareitem')->load($orderToPrepareItemId);
                    $orderItem = $orderToPrepareItem->getSalesOrderItem();
                    $erpSalesFlatOrderItem = Mage::getModel('AdvancedStock/SalesFlatOrderItem')->load($orderItem->getId());
                    $erpSalesFlatOrderItem->setserials($serialNumbers)->save();
                }
            }
            
            //Create shipment
            $shipment = null;
            if (Mage::getStoreConfig('orderpreparation/packing/create_shipment_on_commit')) {
                $preparationWarehouseId = mage::helper('Orderpreparation')->getPreparationWarehouse();
                $operatorId = mage::helper('Orderpreparation')->getOperator();
                $shipment = Mage::helper('Orderpreparation/Shipment')->CreateShipment($order, $preparationWarehouseId, $operatorId);

                //create invoice (if set)
                if (mage::getStoreConfig('orderpreparation/packing/create_invoice_on_commit') == 1) {
                    if (!Mage::helper('Orderpreparation/Invoice')->InvoiceCreatedForOrder($order->getid())) {
                        Mage::helper('Orderpreparation/Invoice')->CreateInvoice($order);
                    }
                }
            }

            //reload order to prepare (to consider new shipment / invoice)
            $orderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($orderId, 'order_id');
            
            //Print packing slip (if configured)
            if (Mage::getStoreConfig('orderpreparation/packing/print_packing_slip_when_order_packed')) {
                if ($shipment) {
                    switch (Mage::getStoreConfig('orderpreparation/order_preparation_step/print_method')) {
                        case 'send_to_printer':
                            $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($shipment));
                            $fileName = 'shipment_' . $shipment->getincrement_id() . '.pdf';
                            mage::helper('ClientComputer')->printDocument($pdf->render(), $fileName, $fileName);
                            break;
                    }
                }
            }

            //Print invoice (if configured)
            if (Mage::getStoreConfig('orderpreparation/packing/print_invoice_when_order_packed')) {
                $invoiceIncrementId = $orderToPrepare->getinvoice_id();
                if ($invoiceIncrementId) {
                    $invoice = Mage::getModel('sales/order_invoice')->load($invoiceIncrementId, 'increment_id');
                    switch (Mage::getStoreConfig('orderpreparation/order_preparation_step/print_method')) {
                        case 'send_to_printer':
                            $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
                            $fileName = 'invoice_' . $invoice->getincrement_id() . '.pdf';
                            mage::helper('ClientComputer')->printDocument($pdf->render(), $fileName, $fileName);
                            break;
                    }
                }
                else
                    Mage::getSingleton('adminhtml/session')->addError($this->__('No invoice to print'));
            }

            //print shipping label (if configured)
            if (Mage::getStoreConfig('orderpreparation/packing/print_shipping_label')) {
                $template = mage::helper('Orderpreparation/CarrierTemplate')->getTemplateForOrder($order);
                if ($template != null) {
                    $collection = mage::getModel('Orderpreparation/ordertoprepare')
                            ->getCollection()
                            ->addFieldToFilter('order_id', $orderId);
                    $content = $template->createExportFile($collection);
                    switch (Mage::getStoreConfig('orderpreparation/order_preparation_step/print_method')) {
                        case 'send_to_printer':
                            mage::helper('ClientComputer')->copyFile($content, $template->getct_export_filename(), 'directory_' . $template->getct_shipping_method(), 'Print shipping labels for order #' . $order->getIncrementId());
                            if ($template->getct_export_witness_filename() != '') {
                                mage::helper('ClientComputer')->copyFile('XXX', $template->getct_export_witness_filename(), 'directory_' . $template->getct_shipping_method(), 'Witness file for order #' . $order->getIncrementId());
                            }
                            break;
                    }
                }
                else
                {
                    //if we cant find a carrier template
                    Mage::getSingleton('adminhtml/session')->addError($this->__('No carrier template available for this order, unable to print the shipping label'));
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Packing commited'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__($ex->getMessage()));
        }

        //redirect
        $this->_redirect('OrderPreparation/Packing', array('order_id' => $orderId));
    }

    /**
     * Print shipment PDF 
     */
    public function printShipmentAction() {
        
        //load shipment
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipmentIncrementId = $this->getRequest()->getParam('shipment_increment_id');
        if ($shipmentId)
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        else
            $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
            
        //generate & download PDF
        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($shipment));
        $fileName = 'shipment_' . $shipment->getIncrementId() . '.pdf';
        $this->_prepareDownloadResponse($fileName, $pdf->render(), 'application/pdf');
    }
    
    
    /**
     * Print Packing slip PDF 
     */
    public function pslippdfAction() {
        
        //load shipment
        $order_id = $this->getRequest()->getPost('order_id');
        $items = $this->getRequest()->getPost('item');
        $pitem = '';
        $new_pitem = '';
        foreach ($items as $item):
            $pitem[] = $item['pro'];
        endforeach;
        foreach ($items as $item):
            $new_pitem[$item['pro']] = $item['qty'];
        endforeach;
        //$this->getRequest()->getPost()
        //echo array_key_exists(1, $item);
        
        //echo '<pre>';print_r($pitem);echo '</pre>';exit;
        //generate & download PDF
        /*$pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($shipment));
        $fileName = 'shipment_' . $shipment->getIncrementId() . '.pdf';
        $this->_prepareDownloadResponse($fileName, $pdf->render(), 'application/pdf');*/
        
        $orders = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $orderItems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id',$orders['entity_id']);
        $billingAddress = $orders->getBillingAddress();
        $shippingAddress = $orders->getShippingAddress();
        $order = $orders->getData();
        //echo '<pre>';print_r($order);echo '</pre>';
        //echo '<pre>';print_r($orderItems->getData());exit;
        $billing = $billingAddress->getData();
        $shipping = $shippingAddress->getData();
        $payment = $orders->getPayment()->getMethodInstance()->getTitle();
        
        //Make it by Jaydip
        $pdf = new Zend_Pdf();
        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES);
        $page->setFont($font, 11);
        
        $this->insertLogo($page, 1);
        //$this->insertAddress($page, 1);
        
        //add pages to main document
        $pdf->pages[] = $page;
        $top = $this->y;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        
        //Header order
        $titre = "Packingslip # ".$order['increment_id'];
        $page->drawText($titre, 40, $page->getHeight()-115, "UTF-8");
        $titre = "Order # ".$order['increment_id'];
        $page->drawText($titre, 40, $page->getHeight()-130, "UTF-8");
        $titre = Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
        $orders->getCreatedAtStoreDate(), 'medium', false);
        $page->drawText($titre, 40, $page->getHeight()-145, "UTF-8");
        
        //Create rectangle
        $top -= 55;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, ($top - 25));
        $page->drawRectangle(275, $top, 570, ($top - 25));
        
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->drawText(Mage::helper('sales')->__('Sold to:'), 35, ($top - 15), 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Ship to:'), 285, ($top - 15), 'UTF-8');
        
        //Create rectangle
        $addressesHeight = 110;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->setFont($font, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;
        
        //Billing Address
        $page->drawText(strip_tags(ltrim($billing['firstname'].' '.$billing['lastname'])), 35, $this->y, 'UTF-8');
        $page->drawText(strip_tags(ltrim($billing['company'])), 35, $this->y-15, 'UTF-8');
        $page->drawText(strip_tags(ltrim($billing['street'].',')), 35, $this->y-30, 'UTF-8');
        $page->drawText(strip_tags(ltrim($billing['statecounty'].',')), 35, $this->y-45, 'UTF-8');
        $page->drawText(strip_tags(ltrim($billing['region'].', '.$billing['postcode'])), 35, $this->y-60, 'UTF-8');
        $page->drawText(strip_tags(ltrim($billing['country_id'])), 35, $this->y-75, 'UTF-8');
        $page->drawText(strip_tags(ltrim($billing['telephone'])), 35, $this->y-90, 'UTF-8');
        
        
        //Shipping Address
        $page->drawText(strip_tags(ltrim($shipping['firstname'].' '.$shipping['lastname'])), 285, $this->y, 'UTF-8');
        $page->drawText(strip_tags(ltrim($shipping['company'])), 285, $this->y-15, 'UTF-8');
        $page->drawText(strip_tags(ltrim($shipping['street'].',')), 285, $this->y-30, 'UTF-8');
        $page->drawText(strip_tags(ltrim($shipping['statecounty'].',')), 285, $this->y-45, 'UTF-8');
        $page->drawText(strip_tags(ltrim($shipping['region'].', '.$shipping['postcode'])), 285, $this->y-60, 'UTF-8');
        $page->drawText(strip_tags(ltrim($shipping['country_id'])), 285, $this->y-75, 'UTF-8');
        $page->drawText(strip_tags(ltrim($shipping['telephone'])), 285, $this->y-90, 'UTF-8');
        $this->y -= 110;
        $addressesEndY = $this->y;
        
        $addressesEndY = min($addressesEndY, $this->y);
        $this->y = $addressesEndY;
        
        //Create rectangle
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 275, $this->y-25);
        $page->drawRectangle(275, $this->y, 570, $this->y-25);
        
        //Payment and Shipping Method
        $this->y -= 15;
        $page->setFont($font, 12);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');
        
        $this->y -=10;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

        $page->setFont($font, 10);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        
        $paymentLeft = 35;
        $yPayments   = $this->y - 15;
        
        foreach (Mage::helper('core/string')->str_split($payment, 45, true, true) as $_value) {
            $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
            $yPayments -= 15;
        }
        $yPayments = min($addressesEndY, $yPayments)-60;
        $page->drawLine(25,  ($top - 150), 25,  $yPayments);
        $page->drawLine(570, ($top - 150), 570, $yPayments);
        $page->drawLine(25,  $yPayments,  570, $yPayments);
        
        foreach (Mage::helper('core/string')->str_split($order['shipping_description'], 45, true, true) as $_value) {
            $page->drawText(strip_tags(trim($_value)), 285, $this->y-15, 'UTF-8');
            $this->y -= 15;
        }
        $topMargin    = 20;
        $methodStartY = $this->y;
        $yShipments = $this->y;
        
        $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') ." ". $orders->formatPriceTxt($orders->getShippingAmount()).")";
        $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
        
        $currentY = min($yPayments, $yShipments);
        // replacement of Shipments-Payments rectangle block
        $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
        $page->drawLine(25,  $currentY,     570, $currentY); //bottom
        $page->drawLine(570, $currentY,     570, $methodStartY); //right
        $this->y = $currentY;
        $this->y -= 15;
        
        //Items
        $page->setFont($font, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
        
        $page->drawText(Mage::helper('sales')->__('Qty'), 35, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Products'), 125, $this->y , 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Sku'), 550, $this->y , 'UTF-8');
        
        $this->y = $this->y-15;
        foreach ($orders->getAllItems() as $_value) {
            if(in_array($_value->getProductId(),$pitem)){
                $this->y -= 15;
                $qty = (isset($new_pitem[$_value->getProductId()]) || $new_pitem[$_value->getProductId()])?$new_pitem[$_value->getProductId()]:floor($_value->getQtyOrdered());
                $page->drawText(strip_tags($qty), 35, $this->y, 'UTF-8');
                $page->drawText(strip_tags(trim($_value->getName())), 125, $this->y, 'UTF-8');
                $page->drawText(strip_tags(trim($_value->getSku())), 500, $this->y, 'UTF-8');
            }   
        }
        $content =  $pdf->render();
        $fileName = 'packingslip_' . $orders->getIncrementId() . '.pdf';
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getBaseDir('media').'/wysiwyg/logo.png';
        if ($image) {
            $image = Mage::getBaseDir('media').'/wysiwyg/logo.png';
            
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }
    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param  string $string
     * @param  Zend_Pdf_Resource_Font $font
     * @param  float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ?
            iconv('UTF-8', 'UTF-16BE//IGNORE', $string) :
            @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }
    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @param  int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }
    
    /**
     * Insert address to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        $array = array(0=>'jesmtest');
        foreach ($array as $value){
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(trim(strip_tags($_value)),
                        $this->getAlignRight($_value, 130, 440, $font, 10),
                        $top,
                        'UTF-8');
                    $top -= 10;
                }
            }
        }
        $this->y = ($this->y > $top) ? $top : $this->y;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Print invoice PDF 
     */
    public function printInvoiceAction() {
        
        //load invoice
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $invoiceIncrementId = $this->getRequest()->getParam('invoice_increment_id');
        
        if ($invoiceId)
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
        else
            $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);

        //generate & download PDF
        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
        $fileName = 'invoice_' . $invoice->getIncrementId() . '.pdf';
        $this->_prepareDownloadResponse($fileName, $pdf->render(), 'application/pdf');
    }
    
    /**
     * Download file for shipping software
     */
    public function downloadShippingLabelFileAction()
    {
        //load invoice
        $orderId = $this->getRequest()->getParam('order_id');
        $orderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($orderId, 'order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $carrierTemplate = mage::helper('Orderpreparation/CarrierTemplate')->getTemplateForOrder($order);
        if ($carrierTemplate == null)
        {
            $fileName = 'error_no_template_found_for_order.csv';
            $this->_prepareDownloadResponse($fileName, '', 'text/csv');
        }
        else
        {
            $collection = mage::getModel('Orderpreparation/ordertoprepare')
                    ->getCollection()
                    ->addFieldToFilter('order_id', $orderId);
            $content = $carrierTemplate->createExportFile($collection);  
            
            $fileName = 'order_' . $order->getIncrementId() . '.csv';
            $this->_prepareDownloadResponse($fileName, $content, 'text/csv');
        }
        
    }

}
