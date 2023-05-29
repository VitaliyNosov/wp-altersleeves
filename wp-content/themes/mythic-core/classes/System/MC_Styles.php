<?php

namespace Mythic_Core\System;

use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Utils\MC_Assets;
use Mythic_Core\Utils\MC_File_Locations;
use Mythic_Core\Utils\MC_Url;

/**
 * Class MC_Styles
 *
 * @package Mythic_Core\System
 */
class MC_Styles {
    
    /**
     * @return array
     */
    public static function deregisters() : array {
        return apply_filters( 'mc-deregister_styles', [] );
    }
    
    /**
     * @return array
     */
    public static function inlines() : array {
        return apply_filters( 'mc-inline_styles', [] );
    }
    
    /**
     * @return array
     */
    public static function files() : array {
        return [
            'mc-'.$handle = '404'                    => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssLayoutsUrl( $handle ),
                'condition' => is_404()
            ],
            'mc-'.$handle = 'affiliation'            => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssModulesUrl( $handle ),
                'condition' => apply_filters( 'mc-affiliation_enqueue', true ) // @todo update to false and add correct filters
            ],
            'mc-'.$handle = 'breadcrumbs'            => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssSectionsUrl( $handle ),
                'condition' => is_page() || is_single(),
                'deps'      => 'mc-bootstrap'
            ],
            'mc-'.$handle = 'bootstrap'              => [
                'handle' => $handle,
                'url'    => MC_File_Locations::bootstrapCssUrl()
            ],
            'mc-'.$handle = 'bootstrap-tagsinput'    => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssVendorUrl( $handle ),
                'deps'   => [ 'mc-bootstrap' ]
            ],
            'mc-'.$handle = 'cart'                   => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssLayoutsUrl( $handle ),
                'condition' => function_exists( 'is_cart' ) && is_cart()
            ],
            'mc-'.$handle = 'cards'                  => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssComponentsUrl( $handle ),
            ],
            'mc-'.$handle = 'checkout'               => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssLayoutsUrl( $handle ),
                'condition' => function_exists( 'is_checkout' ) && is_checkout() || function_exists( 'is_checkout_pay_page' ) && is_checkout_pay_page()
            ],
            'mc-'.$handle = 'elements'               => [
                'handle' => $handle,
            ],
            'mc-'.$handle = 'flex-table'             => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssComponentsUrl( $handle ),
            ],
            'mc-'.$handle = 'font-awesome'           => [
                'handle' => $handle,
                'url'    => MC_File_Locations::fontAwesomeUrl()
            ],
            'mc-'.$handle = 'footer'                 => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssSectionsUrl( $handle )
            ],
            'mc-'.$handle = 'form-fields'            => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssComponentsUrl( $handle ),
            ],
            'mc-'.$handle = 'framework'              => [
                'handle' => $handle,
                'deps'   => [ 'mc-bootstrap', 'mc-hamburger' ]
            ],
            'mc-'.$handle = 'hamburger'              => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssComponentsUrl( $handle )
            ],
            'mc-'.$handle = 'hover'                  => [
                'handle' => $handle,
                'url'    => MC_File_Locations::hoverCssUrl()
            ],
            'mc-'.$handle = 'licensing'              => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssModulesUrl( $handle )
            ],
            'mc-'.$handle = 'login'                  => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssSectionsUrl( 'forms' ),
                'condition' => MC_Url::isLoginPage()
            ],
            'mc-'.$handle = 'mana-icons'             => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssComponentsUrl( 'mtg/'.$handle )
            ],
            'mc-'.$handle = 'marketing'              => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssModulesUrl( 'marketing' ),
                'condition' => MC_User_Functions::isAdmin()
            ],
            'mc-'.$handle = 'posts'                  => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssLayoutsUrl( $handle ),
                'condition' => is_page() || is_single( 'post' )
            ],
            'mc-'.$handle = 'product'                => [
                'handle'    => $handle,
                'url'       => MC_Assets::getCssLayoutsUrl( $handle ),
                'condition' => function_exists( 'is_product' ) && is_product()
            ],
            'mc-'.$handle = 'product-rights-sharing' => [
                'handle' => $handle,
            ],
            'mc-'.$handle = 'profile'                => [
                'handle'    => $handle,
                'condition' => is_author()
            ],
            'mc-'.$handle = 'root'                   => [
                'handle' => $handle
            ],
            'mc-'.$handle = 'select2-js'             => [
                'handle' => $handle,
                'url'    => MC_File_Locations::select2CssUrl()
            ],
            'mc-'.$handle = 'search'                 => [
                'handle' => $handle,
            ],
            'mc-'.$handle = 'search-autocomplete'    => [
                'handle' => $handle
            ],
            'mc-'.$handle = 'style'                  => [
                'handle' => $handle,
                'deps'   => 'mc-root'
            ],
            'mc-'.$handle = 'text'                   => [
                'handle' => $handle,
                'deps'   => 'mc-root'
            ],
            'mc-'.$handle = 'wordpress'              => [
                'handle' => $handle,
                'url'    => MC_Assets::getCssComponentsUrl( $handle )
            ],
            'mc-'.$handle = 'video-js'               => [
                'handle' => $handle,
                'url'    => MC_File_Locations::videoCssUrl()
            ],
        ];
    }
    
}