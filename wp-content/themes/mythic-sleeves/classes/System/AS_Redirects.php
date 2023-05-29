<?php

namespace Alter_Sleeves\System;

use MC_Alter_Functions;
use MC_Licensing_Functions;
use MC_Product_Functions;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Objects\MC_Shortlink;
use Mythic_Core\System\MC_Redirects;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Redirects
 *
 * @package Alter_Sleeves
 */
class AS_Redirects {
    
    /**
     * Redirects constructor
     */
    public function __construct() {
        add_action( 'template_redirect', [ self::class, 'marketing' ], 999999 );
        add_action( 'template_redirect', [ self::class, 'templates' ], 9999 );
        add_action( 'template_redirect', [ self::class, 'redirectFromId' ], 99999 );
        add_action( 'template_redirect', [ MC_Product_Functions::class, 'snapboltRedirect' ], 999 );
    }
    
    public static function templates() {
        if( is_singular( 'design' ) ) {
            $design   = get_queried_object();
            $idDesign = $design->ID;
            $idAlter  = MC_Alter_Functions::design_alter( $idDesign );
            if( empty( $idAlter ) ) MC_Redirects::home();
            wp_redirect( get_the_permalink( $idAlter ) );
            die();
        } else if( is_singular( 'product' ) ) {
            $idProduct = get_queried_object()->ID;
            $idUser    = is_user_logged_in() ? wp_get_current_user()->ID : 0;
            
            if( in_array($idProduct, MC_Licensing_Functions::getAllSharedProductIds())) return;
            if(
                !MC_User_Functions::isAdmin() && !MC_User_Functions::isMod() &&
                $idUser != MC_WP::authorId( $idProduct ) &&
                get_post_status( $idProduct ) !== 'publish' &&
                !MC_Licensing_Functions::userPublisherOfProduct( $idProduct )
            ) {
                MC_Redirects::home();
            }
        }
        if( is_category() || is_attachment() || ( is_tax() ) || is_singular( 'printing' ) || is_singular( 'backer' ) ) {
                MC_Redirects::home();
        }
    }
    
    public static function admin() {
        if( MC_User_Functions::isAdmin() ) return;
        $url = $_SERVER['REQUEST_URI'];
        if( strpos( $url, '/admin' ) !== false ) {
            wp_redirect( MC_SITE );
            die();
        }
    }
    
    public static function jsHome() {
        include( TP_SCRIPTS.'redirect.php' );
    }
    
    public static function redirectFromId() {
        $term = get_search_query();
        if( empty( $term ) ) return;
        $term = trim( $term );
        
        if( is_string( $term ) && !is_numeric( $term ) ) {
            $card_id = MC_Mtg_Card_Functions::id( $term );
            if( !empty( $card_id ) ) {
                wp_redirect( get_site_url().'/browse?browse_type=cards&card_id='.$card_id );
                die();
            }
        }
        
        if( !is_numeric( $term ) ) return;
        $term   = (int) $term;
        $object = get_post( $term );
        if( empty( $object ) ) return;
        $type = get_post_type( $object );
        if( $type != 'product' && MC_User_Functions::isAdmin() ) {
            wp_redirect( '/wp-admin/post.php?post='.$term.'&action=edit' );
            exit();
        } else {
            if( $type == 'backer' ) {
                MC_Redirects::home();
            } else {
                $url = get_the_permalink( $term );
                if( empty( $url ) ) MC_Redirects::home();
                wp_redirect( $url );
                exit();
            }
        }
        if( is_search() && !MC_User_Functions::isAdmin() ) MC_Redirects::home();
    }
    
    public static function partners() {
        $url = $_SERVER['REQUEST_URI'];
        if( strpos( $url, '/' ) === 0 ) $url = substr( $url, 1 );
        $path     = preg_replace( '/\?.*/', '', $url );
        $path     = strtolower( $path );
        $redirect = '';
        $coupon   = false;
        
        $get_redirect_by_affiliate_nicename = MC_Affiliates::getAffiliateSlugByNicename( $path );
        
        if( !empty( $get_redirect_by_affiliate_nicename ) ) {
            $redirect = $get_redirect_by_affiliate_nicename;
            $coupon   = true;
        } else {
            switch( $path ) {
                case 'affinityforcommander' :
                    $redirect = '/content-creator/affinityforcommander';
                    $coupon   = true;
                    break;
                case 'affinityartifacts' :
                    $redirect = '/content-creator/affinityartifacts';
                    $coupon   = true;
                    break;
                case 'cardboardcommand' :
                    $redirect = '/content-creator/cardboardcommand';
                    $coupon   = true;
                    break;
                case 'casuallycompetitive' :
                    $redirect = '/content-creator/casuallycompetitive';
                    $coupon   = true;
                    break;
                case 'commandersquarters' :
                    $redirect = '/content-creator/commandersquarters';
                    $coupon   = true;
                    break;
                case 'drinksofalara' :
                    $redirect = '/content-creator/drinksofalara';
                    $coupon   = true;
                    break;
                case 'edhrecast' :
                    $path     = 'edhrecast';
                    $redirect = '/content-creator/edhrecast';
                    $coupon   = true;
                    break;
                case 'garbageandy' :
                    $path     = 'garbageandy';
                    $redirect = '/content-creator/garbageandy';
                    $coupon   = true;
                    break;
                case 'ihateyourdeck' :
                    $redirect = '/content-creator/ihateyourdeck';
                    $coupon   = true;
                    break;
                case 'magicmics' :
                    $redirect = '/content-creator/magicmics';
                    $coupon   = true;
                    break;
                case 'manacurves' :
                    $redirect = '/content-creator/manacurves';
                    $coupon   = true;
                    break;
                case 'rampgang' :
                case 'mentalmisplay' :
                    $redirect = '/content-creator/mentalmisplay';
                    $coupon   = true;
                    break;
                case 'mtgmuddstah' :
                    $redirect = '/content-creator/mtg-muddstah';
                    $coupon   = true;
                    break;
                case 'mtglexicon' :
                    $redirect = '/content-creator/mtglexicon';
                    $coupon   = true;
                    break;
                case 'playingwithpower' :
                    $redirect = '/content-creator/PlayingWithPower';
                    $coupon   = true;
                    break;
                case 'playtowin' :
                    $redirect = '/content-creator/playtowin';
                    $coupon   = true;
                    break;
                case 'pleasantkenobi' :
                    $redirect = '/content-creator/pleasantkenobi';
                    $coupon   = true;
                    break;
                case 'taaliavess' :
                    $redirect = '/content-creator/taaliavess';
                    $coupon   = true;
                    break;
                default :
                    break;
            }
        }
        
        if( $coupon ) {
            $cart                = WC()->cart;
            $get_applied_coupons = $cart->get_applied_coupons();
            if( empty( $get_applied_coupons ) || !in_array( $path, $get_applied_coupons ) ) {
                $cart->apply_coupon( $path );
            }
        }
        
        return $redirect;
    }
    
    public static function marketing() {
        $currentUrl = $_SERVER['REQUEST_URI'];
        $currentUrl = preg_replace( '/\?.*/', '', $currentUrl );
        $slug       = ltrim( $currentUrl, '/' );
        MC_Shortlink::redirectFromSlug( $slug );
    }
    
}