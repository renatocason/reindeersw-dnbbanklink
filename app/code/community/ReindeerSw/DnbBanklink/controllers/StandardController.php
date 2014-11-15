<?php
/**
 * @copyright Copyright (c) 2014 Reindeer Software (http://reindeersw.com)
 */
class ReindeerSw_DnbBanklink_StandardController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Redirect form
     */
    public function redirectAction() {
        iconv_set_encoding("internal_encoding", "UTF-8");
        iconv_set_encoding("output_encoding", "Windows-1257");
        
        $this -> getResponse()
                -> setHeader("Content-Type", "text/html; charset=Windows-1257", true)
                -> setBody($this -> getLayout() -> createBlock('reindeersw_dnbbanklink/standard_redirect') -> toHtml());
    }
    
    /**
     * Payment canceled
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        if ($session -> getLastRealOrderId()) {
            $order = Mage::getModel('sales/order') -> loadByIncrementId($session -> getLastRealOrderId());
            if ($order -> getId()) {
                $order -> cancel() -> save();
            }
        }
        $this -> _redirect('checkout/cart');
    }

    /**
     * Successful payment
     */
    public function  successAction()
    {
        Mage::getSingleton('checkout/session') -> getQuote() -> setIsActive(false) -> save();
        $this -> _redirect('checkout/onepage/success', array('_secure'=>true));
    }
    
    public function return_manualAction() {
        $params = $this -> getRequest() -> getParams();
        
        Mage::log($params, null, 'reindeersw_dnbbanklink.log', true);
        
        if($params['VK_SERVICE'] == '1101')
            $this -> successAction();
        else
            $this -> cancelAction();
    }
    
    /**
     * Server to server call
     */
    public function returnAction() {
        $params = $this -> getRequest() -> getParams();
        $mac_helper = Mage::helper('reindeersw_dnbbanklink/mac');
        
        // Response logging
        Mage::log($params, null, 'reindeersw_dnbbanklink.log', true);
        
        // MAC Check
        $mac = $mac_helper -> Compose($params);
        
        if(!$mac_helper -> Verify($mac, $params['VK_MAC'])) {
            Mage::log('Wrong MAC', null, 'reindeersw_dnbbanklink.log', true);
            return;
        }
        
        // Load order
        $order = Mage::getModel('sales/order')
            -> loadByIncrementId($params['VK_REF']);
        
        // Canceled
        if($params['VK_SERVICE'] != '1101') {
            if($order -> getStatus() != "canceled")
                $order -> cancel() -> save();
            
            Mage::log('Order canceled', null, 'reindeersw_dnbbanklink.log', true);
            return;
        }
        
        // Save payment details
        $payment = $order -> getPayment();
        $escapedParams = array();
        foreach($params as $key => $value)
            $escapedParams[$key] = $value;
        $payment -> setAdditionalData(serialize($escapedParams));
        $payment -> save();
        
        // Invoicing
        $invoice = Mage::getModel('sales/service_order', $order)
            -> prepareInvoice();

        $invoice -> setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
        $invoice -> register();
        $invoice -> getOrder() -> setCustomerNoteNotify(false);
        $invoice -> getOrder() -> setIsInProcess(true);

        // Save in transaction
        $order -> addStatusHistoryComment('Invoice generation after DNB Banklink payment', false);
        $transactionSave = Mage::getModel('core/resource_transaction')
            -> addObject($invoice)
            -> addObject($invoice -> getOrder());
        $transactionSave -> save();
    
        // Notify customer
        $order -> sendNewOrderEmail() -> addStatusHistoryComment(
                    $this -> __('Notified customer about invoice #%s.', $invoice->getIncrementId())
                )
                -> setIsCustomerNotified(true)
                -> save();
        
        $order  -> setState(Mage_Sales_Model_Order::STATE_PROCESSING)
                -> setStatus('processing')
                -> save();
    }
}