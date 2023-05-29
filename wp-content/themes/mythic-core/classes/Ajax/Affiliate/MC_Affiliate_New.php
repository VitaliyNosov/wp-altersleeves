<?php

namespace Mythic_Core\Ajax\Affiliate;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Affiliate_New
 *
 * @package Mythic_Core\Ajax\Affiliate
 */
class MC_Affiliate_New extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'affiliateData' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $this->success( MC_Affiliates::registerNewAffiliate( $_POST['affiliateData'] ) );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcRegisterNewAffiliate';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'mc_affiliate_data';
    }
    
}
