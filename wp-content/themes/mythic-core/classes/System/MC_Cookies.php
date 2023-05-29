<?php

namespace Mythic_Core\System;

use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Cookies
 *
 * @package Mythic_Core\System
 */
class MC_Cookies {
    
    /**
     * MC_Cookies constructor.
     */
    public function __construct() {
        if( !MC_Access::primarySite() ) return;
        add_action( 'init', [ $this, 'purge' ] );
        add_action( 'init', [ $this, 'partners' ] );
    }
    
    public function partners() {
        if( !empty( $_GET['coupon'] ) ) setcookie( 'coupon', $_GET['coupon'], time() + ( 86400 * 30 ), "/" );
        
        $url        = $_SERVER['REQUEST_URI'];
        $breakables = [ 'dashboard', 'product', 'browse' ];
        foreach( $breakables as $breakable ) {
            if( strpos( $url, $breakable ) !== false ) return;
        }
        
        if( strpos( $url, '/' ) === 0 ) $url = substr( $url, 1 );
        $path         = preg_replace( '/\?.*/', '', $url );
        $path         = strtolower( $path );
        $affiliate_id = MC_Affiliates::userCouponToId( $path );
        
        if( empty( $affiliate_id ) ) return;
        $cookies = json_decode( stripslashes( $_COOKIE['content_creator'] ?? '' ), true );
        $cookies = is_array( $cookies ) ? $cookies : [];
        if( !in_array( $affiliate_id, $cookies ) ) {
            $affiliations         = get_option( 'acquisition_'.$affiliate_id, [] );
            $month                = date( 'm' );
            $year                 = date( 'Y' );
            $key                  = $year.'-'.$month;
            $monthlyCount         = isset( $affiliations[ $key ] ) ? $affiliations[ $key ] : 0;
            $affiliations[ $key ] = $monthlyCount + 1;
            update_option( 'acquisition_'.$affiliate_id, $affiliations );
        }
        $cookies[ time() ] = $affiliate_id;
        setcookie( 'content_creator', json_encode( $cookies, JSON_UNESCAPED_SLASHES ), time() + ( 86400 * 30 ), "/" );
    }
    
    /**
     *
     */
    public function purge() {
        $cookies = $_COOKIE;
        if( count( $cookies ) < 20 ) return;
        foreach( $cookies as $key => $cookie ) {
            setcookie( $key, null, -1, '/' );
        }
    }
    
}