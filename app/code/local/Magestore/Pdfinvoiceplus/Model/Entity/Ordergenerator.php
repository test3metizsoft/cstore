<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator extends Magestore_Pdfinvoiceplus_Model_Entity_Abstractextend {

    const THE_START = '##productlist_start##';
    const THE_END = '##productlist_end##';
    public $pdfCollection;

    /**
     * The pdf proceesed template
     * @var string
     */
    protected $_pdfProcessedTemplate;

    /**
     * Load the pdf system
     * @return Mpdf Object - lib
     */
    protected function _construct()
    {
        $this->setPdfCollection();
        parent::_construct();
    }

    public function setTheSourceId($orderId)
    {
        $this->_sourceId = $orderId;
        return $this->_sourceId;
    }

    public function getTheSourceId()
    {
        return $this->_sourceId;
    }
     public function setPdfCollection()
    {
        $this->pdfCollection = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        return $this;
    }

    public function getPdfCollection()
    {
        return $this->pdfCollection;
    }

    public function getTheStoreId()
    {
        if ($storeId = $this->getTheOrder()->getStore()->getId())
        {
            return array(0, $storeId);
        }
        return array(0);
    }

    public function getFilteredCollection()
    {
        try {
            $pdfGeneratorTemplate = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if ($this->templateId)
//            {
//                $pdfGeneratorTemplate = $this->getPdfCollection()
//                        ->addFieldToSelect('*')
//                        ->addFieldToFilter('template_id',$this->templateId);
//            }
//            else
//            {
//                $pdfGeneratorTemplate = $this->getPdfCollection()
//                        ->addFieldToSelect('*')
//                        ->addFieldToFilter('status', 1);
////                        ->addFieldToFilter('template_store_id', $this->getTheStoreId());
//            }
            
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $pdfGeneratorTemplate;
    }

    public function createTemplate()
    {
        $pdfCollection = $this->getFilteredCollection();
        if ($pdfCollection)
        {
            return $pdfCollection;
        }
//        foreach ($pdfCollection as $pdf)
//        {
//            $dataTemplate = $pdf;
//        }

//        if ($dataTemplate)
//        {
//            return $dataTemplate;
//        }
        return false;
    }

    public function getFileName() {
        if ($fileName = $this->createTemplate()->getData('order_filename'))
        {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($fileName);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);

            $cleanString = Mage::helper('core/string')->cleanString($processedTemplate);
            $cleanString = str_replace(array(' ', '.', ':'), '-', $processedTemplate);
            return $cleanString;
        }
        return 'order - ';
    }
     public function getBody()
    {

        if ($body = $this->createTemplate()->getData('order_html'))
        {
            return $body;
            
        }
        return false;
    }

    /**
     * Get the template body from used in the backend with the varables and add the item variables.
     * @return string
     */
    public function getTheTemplateBodyWithItems()
    {
        $templateToProcessForItems = $this->getBody();
          /* Change by Zeus 08/12 */
        $finalItems = NULL;
        /* End change */
        $items = Mage::getModel('pdfinvoiceplus/entity_itemsorder')
                        ->setSource($this->getTheOrder())->setOrder($this->getTheOrder());
        $itemsData = $items->processAllVars();
        
        $result = Mage::helper('pdfinvoiceplus/items')
                ->getTheItemsFromBetwin($templateToProcessForItems,self::THE_START, self::THE_END);
        $i = 1;
        foreach ($itemsData as $templateVars)
        {
            $itemPosition = array('items_position' => $i++);
            $templateVars = array_merge($itemPosition, $templateVars);
            $pdfProcessTemplate = Mage::getModel('core/email_template');
            $itemProcess = $pdfProcessTemplate->setTemplateText($result)->getProcessedTemplate($templateVars);
//            if($i%2==0){
//                $itemProcess = str_replace('<tr class="items-tr background-items">','<tr>',$itemProcess);
//            }
            $finalItems .= $itemProcess . '<br>';
        }
        $templateWithItemsProcessed = str_replace($result, $finalItems, $templateToProcessForItems);

        $tempmplateForHtmlProcess = '<html>' . $templateWithItemsProcessed . '</html>';

        //$htmlTemplateWithItems = Mage::helper('pdfinvoiceplus/items')->processHtml($tempmplateForHtmlProcess);

        return $tempmplateForHtmlProcess;
    }

    /**
     * Load the default information for the template processing
     * @return object Mail template object
     */
    public function mainVariableProcess()
    {
        $templateText = $this->getTheTemplateBodyWithItems();
        //auto insert totals
        $total_order = Mage::getModel('pdfinvoiceplus/entity_totalsRender_order');
        $total_order->setSource($this->getTheOrder())
            ->setHtml($templateText)->setTemplateId($this->createTemplate()->getId());
        $templateText = $total_order->renderHtml();
        
        $theVariableProcessor = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($templateText);
        return $theVariableProcessor;
    }

    /**
     * The vars for the entity
     * @return type
     */
    public function getTheProcessedTemplate()
    {
        $templateVars = $this->getVars();
        $processedTemplate = $this->mainVariableProcess()->getProcessedTemplate($templateVars);
        return $processedTemplate;
    }

    /**
     * get system template in use
     * @return system template
     */
    public function getSystemTemplate() {
        $activeTemplate = Mage::getModel('pdfinvoiceplus/template')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
        $systemTemplate = Mage::getModel('pdfinvoiceplus/systemtemplate')
            ->load($activeTemplate->getSystemTemplateId());
        return $systemTemplate;
    }

    public function getOrientation() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $orientation = $template->getOrientation();
        if ($orientation == 1)
            return 'L';
        return 'P';
    }
    /* Change by Jack 26/12 */
    public function isMassPDF(){
        $orderIds = Mage::app()->getRequest()->getPost('order_ids');
        if($orderIds)
            return true;
        return false;
    }
    public function getPdf($html = '') {
        $isMassPDF = $this->isMassPDF();
        $mailPdf = new Varien_Object;
        if($isMassPDF){
            $templateBody = $this->getTheProcessedTemplate();
            $mailPdf->setData('htmltemplate', $templateBody);
            $mailPdf->setData('filename', $this->getFileName());
        }
        else{
            $pdf = $this->loadPdf();
            $templateBody = $this->getTheProcessedTemplate();
            $pdf->WriteHTML($this->getCss(), 1);
            $pdf->WriteHTML($templateBody);

            $mailPdf->setData('htmltemplate', $templateBody);
            $output = $pdf->Output($this->getFileName(), 'S');
            $mailPdf->setData('pdfbody', $output);
            $mailPdf->setData('filename', $this->getFileName());
        }
        return $mailPdf;
    }
    // End Change
    public function pdfPaperFormat() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $format = $template->getFormat();
        $format = $format ? $format : 'A4';
        return $format;
    }

    public function loadPdf() {
		$orderId =Mage::app()->getRequest()->getParam('order_id');
		if(!$orderId){
			$postData = Mage::app()->getRequest()->getPost('order_ids');
			$orderId = $postData[0]; 
		}
		if(count($postData) == 1 || Mage::app()->getRequest()->getParam('order_id')){
				$order = Mage::getModel('sales/order')->load($orderId);
				$incrementId = $order->getIncrementId();
				$status = $order->getStatus();
				$template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
				$createdAt = Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', true);
				$top = '70';
				$bottom = '55';
				$left = '0';
				$right = '0';
				$orientation = $this->getOrientation();
				$pdf = new Mpdf_Magestorepdf('', $this->pdfPaperFormat(), 8, '', $left, $right, $top, $bottom);
				$pdf->AddPage($orientation);
				$pdf->SetY(5);
				//Change by Jack 29/12 - add page number
				   /*  $storeId = Mage::app()->getStore()->getStoreId();
					$isEnablePageNumbering = Mage::getStoreConfig('pdfinvoiceplus/general/page_numbering',$storeId);
					if($isEnablePageNumbering)
					   if($isEnablePageNumbering)
						$pdf->SetHTMLFooter('<div style = "float:right;z-index:16000 !important; width:30px;">{PAGENO}/{nb}</div>'); */
				// End Change  
				$pdf->SetHTMLFooter('<div id="footer" class="footer-template04 color-text style-color" name="myfooter" style="height: 100px;">
				<div contextmenu-type="main" contenteditable="true" title="Click to edit, right-click to insert variable" class="color-text contenteditable" style="padding-left:3.5%;padding-top: 10px;width: 100%; " info-text="footer"><div class="footer-template04 color-text style-color" id="footer" style="border-color: #ff0000; height: auto; background-color: transparent;" name="myfooter">
				<p><img style="text-align: center;" src="http://cstoremaster.com/media/wysiwyg/footer.png"></p>
				</div></div>
				</div>');
				$pdf->SetHTMLHeader('<div id="container-inner" class="template04"><div class="myheader-iv" style="padding-top: 20px; padding-bottom: 20px;">
				<div class="top-header-iv">
					<div class="title-page-iv1">
						<div class="order1 title-color contenteditable color-theme" contextmenu-type="main" title="Click to edit, right-click to insert variable" contenteditable="true" style="">
							Order</div>  
											<div class="barcode" style="">
											</div>
					</div>

					<div class="box-logo ajaxupload" style="margin-top: 0px;text-align: right; float: right" info-img="company_logo" title="Click to upload...">
						<img width="160" src="data:image/png;base64,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">            </div>
				</div>
				<div class="bottom-header-iv" contextmenu-type="main">
					<div class="id-order-iv">
						<div contextmenu-type="main" class="content contenteditable" contenteditable="true">
							<span class="color-text contenteditable" title="Click to edit, right-click to insert variable" contenteditable="true" style="font-family: Ubuntu Medium;font-size: 26px;">#'.$incrementId .'</span><br>
							<span class="color-text contenteditable" title="Click to edit, right-click to insert variable" contenteditable="true" style="color: #010101;font-size: 18px;">'.$createdAt.'</span>
						</div>
						<div class="status color-text contenteditable" title="Click to edit, right-click to insert variable" contenteditable="true" style="margin-top: 5px;">
							<span style="font-weight: bold;">Status: </span> '.$status.'
						</div>
					</div>
					<div contextmenu-type="main" contenteditable="true" title="Click to edit..." class="box-infomations autogrow info-iv contenteditable">
						<span class="title-color" title="Click to edit, right-click to insert variable" style="display: block; font-weight: bold; font-size: 18px; color: #CC0000; width:100%; float: left;font-family: Ubuntu;text-transform: uppercase;" info-text="company_name">cStoreMaster</span><br>                <span class="color-text" title="Click to edit, right-click to insert variable" style="display: block; font-family: Ubuntu Light; font-size: 14px;width:100%;float: left;"><strong>Address: </strong><info info-text="company_address">4320 University Dr , Huntsville , AL 5816</info></span><br>		 
																										
					</div>
				</div>												
			</div></div>');
		}
		else{
			$template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
			$top = '5';
			$bottom = '0';
			$left = '0';
			$right = '0';
			$orientation = $this->getOrientation();
			$pdf = new Mpdf_Magestorepdf('', $this->pdfPaperFormat(), 8, '', $left, $right, $top, $bottom);
			$pdf->AddPage($orientation);
        //Change by Jack 29/12 - add page number
            $storeId = Mage::app()->getStore()->getStoreId();
            $isEnablePageNumbering = Mage::getStoreConfig('pdfinvoiceplus/general/page_numbering',$storeId);
            if($isEnablePageNumbering)
               if($isEnablePageNumbering)
                $pdf->SetHTMLFooter('<div style = "float:right;z-index:16000 !important; width:30px;">{PAGENO}/{nb}</div>');
		}
        return $pdf;
    }
}
