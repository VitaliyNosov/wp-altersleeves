<?php

namespace Mythic_Core\Ajax\Affiliate;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Affiliate_Get_Data
 *
 * @package Mythic_Core\Ajax\Affiliate
 */
class MC_Affiliate_Get_Data extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'userId' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $this->success( MC_Affiliates::getAffiliateData( $_POST['userId'] ) );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcGetAffiliateData';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_affiliate_data';
    }
    
}
