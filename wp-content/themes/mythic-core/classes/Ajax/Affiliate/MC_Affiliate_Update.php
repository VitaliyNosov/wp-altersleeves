<?php

namespace Mythic_Core\Ajax\Affiliate;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Affiliate_Update
 *
 * @package Mythic_Core\Ajax\Affiliate
 */
class MC_Affiliate_Update extends MC_Ajax {
    
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
        $this->success( MC_Affiliates::updateAffiliateData( $_POST['affiliateData'] ) );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcUpdateAffiliate';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'affiliate_data';
    }
    
}
