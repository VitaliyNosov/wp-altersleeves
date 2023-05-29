<?php

namespace Mythic_Core\Functions;

use Exception;
use MC_User;
use WC_Customer;

/**
 * Class MC_Woo_Customer_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Woo_Customer_Functions {
    
    /**
     * @param int $user_id
     *
     * @return WC_Customer
     * @throws Exception
     */
    public static function getCustomer( int $user_id = 0 ) : ?WC_Customer {
        if( !MC_WOO_ACTIVE ) return null;
        if( empty( $user_id ) ) $user_id = MC_User::id();
        return !empty( $user_id ) ? new WC_Customer( $user_id ) : new WC_Customer();
    }
    
}