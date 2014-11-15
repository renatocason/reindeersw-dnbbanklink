<?php
/**
 * @copyright Copyright (c) 2014 Reindeer Software (http://reindeersw.com)
 */
class ReindeerSw_DnbBanklink_Block_Standard_Redirect extends Mage_Core_Block_Abstract {
    
    protected function _toHtml() {
        $standard = Mage::getModel('reindeersw_dnbbanklink/standard');

        $submitUrl = 'http://172.16.38.4:81/loginb2b.aspx';
        if(!$standard -> getConfig() -> getTestMode())
            $submitUrl = $standard -> getConfig() -> getSubmitUrl();
        
        // Generazione HTML
        $html = '<!DOCTYPE html><html><head><title>Payment Redirect</title></head><body>';
        $html .= $this -> __('You will be redirected to the gateway website in a few seconds.');
        $html .= '<form id="reindeersw_dnbbanklink_standard_checkout" method="POST" action="' . $submitUrl . '">' . "\n";
        
        foreach ($standard -> getCheckoutFormFields() as $field => $value)
            $html .= '<input type="hidden" name="' . $field . '" value="' . $value . '" />' . "\n";
        
        $html .= '</form>';
        $html .= '<script type="text/javascript">document.getElementById("reindeersw_dnbbanklink_standard_checkout").submit();</script>';
        $html .= '</body></html>';

        return $html;
    }
    
}