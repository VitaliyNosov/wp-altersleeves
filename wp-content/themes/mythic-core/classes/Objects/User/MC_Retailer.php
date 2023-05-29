<?php

namespace Mythic_Core\Users;

use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Retailer
 *
 * @package Mythic_Core\Users
 */
class MC_Retailer extends MC_User_Functions {
    
    // TODO: create new user role with current name (we can change it before creating)
    public static $role_name = 'retailer';
    
    public $retailer_store_address = [
        'shipping_first_name' => '',
        'shipping_last_name'  => '',
        'shipping_company'    => '',
        'shipping_address_1'  => '',
        'shipping_address_2'  => '',
        'shipping_city'       => '',
        'shipping_postcode'   => '',
        'shipping_country'    => '',
        'shipping_state'      => ''
    ];
    
    public function __construct( $user_id ) {
        parent::__construct();
        
        $this->setRetailerAddress( $user_id );
    }
    
    public function getRetailerAddress() {
        return $this->retailer_store_address;
    }
    
    /**
     * TODO: remove hardcoded address
     * @param $user_id
     */
    public function setRetailerAddress( $user_id ) {
        $this->retailer_store_address = [
            'shipping_first_name' => 'Sergey',
            'shipping_last_name'  => 'Krokhmal',
            'shipping_company'    => 'company_retailer',
            'shipping_address_1'  => 'address_1_retailer',
            'shipping_address_2'  => 'address_2_retailer',
            'shipping_city'       => 'city_retailer',
            'shipping_postcode'   => '12323',
            'shipping_country'    => 'US',
            'shipping_state'      => 'CA',
        ];
    }
    
}