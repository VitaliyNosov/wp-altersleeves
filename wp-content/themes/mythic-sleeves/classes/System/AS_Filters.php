<?php

namespace Alter_Sleeves\System;

use MC_Giftcard_Functions;
use MC_Product_Functions;

/**
 * Class MC_Filters
 *
 * @package Alter_Sleeves\System
 */
class AS_Filters {
    
    /**
     * MC_Filters constructor.
     */
    public function __construct() {
        add_filter( 'mc_disclaimer_filter', [ self::class, 'disclaimer' ] );
        add_filter( 'mc_header_search_groups', [ self::class, 'searchGroups' ] );
        add_filter( 'mc_header_sections', [ self::class, 'headerSections' ] );
        
        $this->vendorFilters();
    }
    
    /**
     * @param $disclaimer
     *
     * @return string
     */
    public static function disclaimer( $disclaimer ) : string {
        return do_shortcode( ' Magic: The Gathering, its logo, the planeswalker symbol, the [mtg_mana_symbols] symbols, the pentagon of colors, and all characters’ names and distinctive likenesses are property of Wizards of the Coast LLC in the USA and other countries. All Rights Reserved.
            ' );
    }
    
    /**
     * @return string[]
     */
    public static function headerSections() {
        return [
            'logo',
            'search',
            'nav',
            'cart',
        ];
    }
    
    /**
     * @return string[]
     */
    public static function searchGroups() : array {
        return [ 'cards', 'artists', 'sets', 'tags', 'content_creators' ];
    }
    
    public static function vendorFilters() {
        // Woocommerce
        if( !MC_WOO_ACTIVE ) return;
        add_filter( 'woocommerce_email_attachments', [
            MC_Giftcard_Functions::class,
            'attachGift_CardToEmail',
        ],          10, 4 );
        add_filter( 'woocommerce_cart_item_name', [ MC_Product_Functions::class, 'cartName' ], 10, 2 );
        add_filter( 'woocommerce_cart_item_thumbnail', [ MC_Product_Functions::class, 'cartImage' ], 10, 3 );
    }
    
}