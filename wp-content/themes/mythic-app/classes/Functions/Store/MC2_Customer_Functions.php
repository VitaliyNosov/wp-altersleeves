<?php

namespace Mythic\Functions\Store;

use Exception;
use Mythic\Functions\User\MC2_User_Functions;
use WC_Customer;

class MC2_Customer_Functions {
    
    /**
     * @param int $user_id
     *
     * @return WC_Customer
     * @throws Exception
     */
    public static function get( int $user_id = 0 ) : ?WC_Customer {
        if( !WOO_ACTIVE ) return null;
        if( empty( $user_id ) ) $user_id = MC2_User_Functions::id();
        return !empty( $user_id ) ? new WC_Customer( $user_id ) : new WC_Customer();
    }
    
}