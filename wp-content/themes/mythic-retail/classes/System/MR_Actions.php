<?php

namespace Mythic_Retail\System;

use MC_User_Functions;
use MC_Woo_Cart_Functions;
use MC_Woo_Order_Functions;
use MC_WP;
use Mythic_Core\Display\MC_Render;

/**
 * Class MR_Actions
 *
 * @package Mythic_Retail\System
 */
class MR_Actions {

    /**
     * MR_Actions constructor.
     */
    public function __construct() {
        add_action( 'mc_header_logo', [ self::class, 'headerLogo' ] );
        add_action( 'mc_header_cart', [ self::class, 'headerCart' ] );
        add_action( 'mc_header_logo', [ self::class, 'headerLogo' ] );
        add_action( 'mc_header_nav', [ self::class, 'headerNav' ] );
        add_action( 'wp_loaded', [ self::class, 'addPreorderProduct' ] );
    }

    public static function headerCart() {
        MC_Render::templatePart( 'header/cart' );
    }

    public static function headerLogo() {
        MC_Render::templatePart( 'header/logo' );
    }

    public static function headerNav() {
        MC_Render::templatePart( 'header/nav' );
    }

    /**
     * @throws \Exception
     */
    public static function addPreorderProduct() {
        if( is_admin()) return;
        $has_user_ordered = MC_Woo_Order_Functions::userHasPreviouslyPurchased( MC_User_Functions::id() );
        if( !empty( $has_user_ordered ) ) return;
        if( empty(MC_WP::meta('business_details', MC_User_Functions::id(), 'user' ))) return;
        $in_cart = MC_Woo_Cart_Functions::productInCart( 125 );
        if( !empty( $in_cart ) ) return;
        $cart = WC()->cart;
        $cart->add_to_cart( 125 );
    }

}