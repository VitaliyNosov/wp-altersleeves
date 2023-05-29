<?php

namespace Mythic_Core\System;

use Mythic_Core\Functions\MC_Woo_Product_Functions;
use Mythic_Core\Objects\MC_Shortlink;
use Mythic_Core\Users\MC_Affiliates;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Utils\MC_Scryfall;
use Mythic_Core\Utils\MC_Url;
use Mythic_Core\Utils\MC_Woo;

/**
 * Class MC_Redirects
 *
 * @package Mythic_Core\System
 */
class MC_Redirects {
    
    public string $search_term = '';
    
    /**
     * MC_Redirects constructor.
     */
    public function __construct() {
        add_action( 'template_redirect', [ $this, 'redirects' ], 2 );
    }
    
    /**
     * The redirect methods in order of preferred action
     */
    public function redirects() {
//        $this->https();
        $this->scryfall_import();
        $this->partners();
        $this->login();
        $this->dashboard();
        $this->shortlinks();
        $this->tickets();
        $term = get_search_query();
        if( empty( $term ) ) return;
        $this->setSearchTerm( $term );
        $this->fromId();
        $this->fromName();
    }
    
    /** Quick Scryfall import */
    private function scryfall_import() {
        if( empty($scryfall_id = $_GET['scryfall_import'] ?? '')) return;
        MC_Scryfall::import_card_by_scryfall_id($scryfall_id);
    }
    
    /**
     * Prevents logged out users from accessing the dashboard
     */
    public static function dashboard() {
        if( MC_Url::isDashboard() && !is_user_logged_in() ) {
            self::redirect( MC_Url::loginUrl() );
        }
    }
    
    /**
     * Redirect to home
     */
    public static function home() {
        self::redirect( MC_SITE );
    }
    
    /**
     * Forces HTTPS url for site
     */
    public function https() {
        if( is_ssl() ) return;
        self::redirect( 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
    }
    
    /**
     * Forces logged in users to the dashboard and logged in users to a non-cached version of the login page
     */
    public function login() {
        if( MC_Url::isLoginPage() && is_user_logged_in() ) {
            self::redirect( MC_Woo::dashboardUrl() );
        }
    }
    
    /**
     * Redirects relating to affiliate partners
     */
    public function partners() {
        $partner_redirect = MC_Affiliates::getAffiliateRedirectFromCurrentUrl();
        if( empty( $partner_redirect ) ) return;
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        if( empty( $query_string ) ) {
            self::redirect( strtok( $_SERVER["REQUEST_URI"], '?' ).'?t='.time() );
        }
        $partner_redirect = $partner_redirect.'?'.$query_string;
        self::redirect( $partner_redirect );
    }
    
    /**
     * Redirects shortlinks
     */
    public function shortlinks() {
        $currentUrl = $_SERVER['REQUEST_URI'];
        $currentUrl = preg_replace( '/\?.*/', '', $currentUrl );
        $slug       = ltrim( $currentUrl, '/' );
        MC_Shortlink::redirectFromSlug( $slug );
    }
    
    /**
     * Redirects front end users to helpdesk home and admins to tickets
     */
    public function tickets() {
        if( !MC_Url::contains( 'ticket-helpdesk' ) ) return;
        $url = MC_User_Functions::isAdmin() ?
            'https://help.mythicgaming.com/support/altersleeves/ShowHomePage.do#Solutions' :
            'https://help.mythicgaming.com';
        self::redirect( $url );
    }
    
    /**
     * Redirects based on ID number from header search
     */
    public function fromId() {
        $term = $this->search_term;
        if( empty( $term ) ) return;
        $term = trim( $term );
        if( !is_numeric( $term ) ) return;
        $term   = (int) $term;
        $object = get_post( $term );
        if( empty( $object ) ) return;
        $type = get_post_type( $object );
        if( $type == 'product' ) MC_Woo_Product_Functions::redirect( $term );
        if( $type != 'product' && MC_User_Functions::isAdmin() ) {
            self::redirect( '/wp-admin/post.php?post='.$term.'&action=edit' );
        }
    }
    
    public function fromName() {
        $term = $this->search_term;
        if( empty( $term ) ) return;
        $post_types = MC_WP::getPostTypeNames();
        foreach( $post_types as $post_type ) {
            if( $post_type == 'attachment' ) continue;
            $object = get_page_by_title( $term, OBJECT, $post_type );
            if( empty( $object ) ) continue;
            self::redirect( '/wp-admin/post.php?post='.$object->ID.'&action=edit' );
        }
    }
    
    /**
     * @param string $search_term
     */
    public function setSearchTerm( string $search_term ) {
        $this->search_term = $search_term;
    }
    
    public static function redirect( $url = '' ) {
        if( empty( $url ) ) return;
        wp_redirect( $url );
        die();
    }
    
}