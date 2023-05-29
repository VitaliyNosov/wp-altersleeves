<?php

namespace Mythic\Functions\API\Partials;

use Mythic\Abstracts\MC2_Class;
use Mythic\Functions\API\MC2_API_Functions;
use Mythic\Functions\Marketing\MC2_Social_Functions;
use Mythic\Functions\Store\Cart\MC2_Cart_Functions;
use Mythic\Functions\User\MC2_User_Functions;
use Mythic\Functions\Website\MC2_Legal_Notices;
use Mythic\Functions\Website\MC2_Logo_Functions;
use Mythic\Functions\Wordpress\MC2_Navigation_Functions;
use Mythic\Helpers\MC2;

class MC2_Global extends MC2_Class {
    
    const CACHE_NAME = 'api_global';
    public $cache;
    
    public function __construct( $cache = true ) {
        parent::__construct();
        $this->set_cache( $cache );
    }
    
    public function actions() {
        add_action( 'wp_enqueue_scripts', [ $this, 'localize_vue_data' ], 99999 );
    }
    
    public function localize_vue_data() {
        global $vue_global_data;
        $cache           = $this->get_cache();
        $vue_global_data = [
            'header_data' => $this->get_header_data( $cache ),
            'footer_data' => $this->get_footer_data( $cache )
        ];
        wp_localize_script( 'ma-vue', 'vue_global_data', $vue_global_data );
    }
    
    /**
     * @param bool $cache
     *
     * @return array
     */
    public function get_header_data( bool $cache = true ) : array {
        if( $cache ) {
            $cached_data = MC2_API_Functions::get_cached_json_data( self::CACHE_NAME );
            if( MC2::array_keys_exists( [ 'logo', 'menu', 'user_data', 'cart_data' ], $cached_data ) ) return $cached_data;
        }
        $data = [
            /*
            'top_promo_line' =>
                [
                    'html'       => 'Use coupon AWESOME for 10% off',
                    'background' => '',
                    'padding'    => '20px',
                ],
            */
            'logo'      => MC2_Logo_Functions::get_header_logo(),
            'menu'      =>
                [
                    'third' => MC2_Navigation_Functions::get_nav_items_for_front( self::CACHE_NAME, )
                ],
            'user_data' => MC2_User_Functions::get_header_data(),
            'cart_data' => MC2_Cart_Functions::get_header_data()
        ];
        
        MC2_API_Functions::update_cached_json_file( self::CACHE_NAME, $data );
        
        return $data;
    }
    
    /**
     * @param bool $cache
     *
     * @return array
     */
    public function get_footer_data( bool $cache = true ) : array {
        if( $cache ) {
            $cached_data = MC2_API_Functions::get_cached_json_data( self::CACHE_NAME );
            if( MC2::array_keys_exists( [ 'logo', 'menus', 'copyright' ], $cached_data ) ) return $cached_data;
        }
        $data = [
            'logo'      => MC2_Logo_Functions::get_footer_logo(),
            'menus'     =>
                [
                    'first'   => MC2_Navigation_Functions::get_nav_items_for_front( 'footer_1' ),
                    'second'  => MC2_Navigation_Functions::get_nav_items_for_front( 'footer_2' ),
                    'third'   => MC2_Navigation_Functions::get_nav_items_for_front( 'footer_3' ),
                    'socials' => MC2_Social_Functions::brand_socials()
                ],
            'copyright' => MC2_Legal_Notices::copyright()
        ];
        
        MC2_API_Functions::update_cached_json_file( self::CACHE_NAME, $data );
        
        return $data;
    }
    
    /**
     * @return bool
     */
    public function get_cache() : bool {
        return $this->cache;
    }
    
    /**
     * @param bool $cache
     */
    public function set_cache( bool $cache ) {
        $this->cache = $cache;
    }
    
}