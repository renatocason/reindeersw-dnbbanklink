<?php
/**
 * @copyright Copyright (c) 2014 Reindeer Software (http://reindeersw.com)
 */
class ReindeerSw_DnbBanklink_Model_Config {
    
    /**
     * 
     * @return boolean
     */
    public function getEnabled() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/active');
    }
    
    /**
     * 
     * @return boolean
     */
    public function getTestMode() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/test_mode');
    }
    
    /**
     * 
     * @return string
     */
    public function getSubmitUrl() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/submit_url');
    }
    
    /**
     * 
     * @return integer
     */
    public function getVkSndID() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/vk_snd_id');
    }
    
    /**
     * 
     * @return integer
     */
    public function getVkAcc() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/vk_acc');
    }
    
    /**
     * 
     * @return string
     */
    public function getVkPank() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/vk_pank');
    }
    
    /**
     * 
     * @return string
     */
    public function getVkName() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/vk_name');
    }
    
    /**
     * 
     * @return string
     */
    public function getClientPrivateKey() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/client_private_key');
    }
    
    /**
     * 
     * @return string
     */
    public function getClientKeyPassphrase() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/client_key_passphrase');
    }
    
    /**
     * 
     * @return string
     */
    public function getBankPublicKey() {
        return Mage::getStoreConfig('payment/reindeersw_dnbbanklink/bank_public_key');
    }
}