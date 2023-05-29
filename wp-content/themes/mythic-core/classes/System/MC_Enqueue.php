<?php

namespace Mythic_Core\System;

use Mythic_Core\Utils\MC_Assets;
use Mythic_Core\Utils\MC_Files;
use Mythic_Core\Utils\MC_Url;
use Mythic_Core\Utils\MC_Vars;

/**
 * Class MC_Enqueue
 *
 * @package Mythic_Core\System
 */
class MC_Enqueue {
    
    /**
     * MC_Scripts_Styles constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'deregister' ], 1 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueues' ], 9999 );
        add_action( 'wp_enqueue_scripts', [ $this, 'inlineChildFiles' ], 9999 );
        add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueues' ], 9999 );
    }
    
    /**
     * Deregister unwanted WP scripts
     *
     * To deregister scripts, add the desired handle to a filter on 'mc_deregister_scripts'
     *
     * To deregister styles, add the desired handle to a filter on 'mc_deregister_styles'
     */
    public function deregister() {
        foreach( [ 'script' => MC_Styles::deregisters(), 'style' => MC_Styles::deregisters() ] as $deregisters ) {
            foreach( $deregisters as $type => $deregister ) 'wp_deregister_'.$type( $deregister );
        }
    }
    
    /**
     * @param string|array $args
     */
    public static function enqueue( $args = [] ) {
        if( is_string( $args ) ) $args = [ 'handle' => $args ];
        extract( $args );
        if( !is_admin() && !empty( $admin ) ) return;
        if( empty( $handle ) ) return;
        $url_parent = isset( $url );
        $type       = self::enqueueType( $type ?? '' );
        if( empty( $url ) && $type == 'style' ) {
            $url        = MC_Assets::getCssUrl( $handle );
            $url_parent = $url_parent ? $url : MC_Assets::getCssUrl( $handle, true );
        } else if( empty( $url ) && $type == 'script' ) {
            $url        = MC_Assets::getJsUrl( $handle );
            $url_parent = $url_parent ? $url : MC_Assets::getJsUrl( $handle, true );
        }
        if( empty( $url ) ) return;
        $file_type = MC_Files::extension( $url );
        if( $file_type != 'css' && $file_type != 'js' ) return;
        $files = [ $url ];
        if( !empty( $url_parent ) && $url_parent != $url ) $files[] = $url_parent;
        $deps = $deps ?? [];
        if( is_string( $deps ) ) $deps = [ $deps ];
        $prefix     = self::prefixer( $prefix ?? '' );
        $child      = false;
        $helpers_js = 'mc-helpers';
        
        foreach( $files as $file ) {
            $full_handle = self::handler( $handle, $prefix, $child );
            $links       = self::linker( $file );
            $url         = $links['url'];
            $path        = $links['path'];
            $version     = file_exists( $path ) ? filemtime( $path ) : false;
            
            if( $file_type == 'css' ) {
                $media = $media ?? 'all';
                wp_enqueue_style( $full_handle, $url, $deps, $version, $media );
                $type    = 'style';
                $inlines = MC_Styles::inlines()[ $full_handle ] ?? false;
            } else {
                // @todo make smarter so prefix not needed if no mc- equivalent (ie mg-products)
                if( $prefix != 'mc-' && !in_array( $helpers_js, $deps ) ) $deps[] = $helpers_js;
                if( !in_array( 'jquery', $deps ) ) $deps[] = 'jquery';
                wp_register_script( $full_handle, $url, $deps, $version, $footer ?? true );
                if( !empty( $localization_args ) ) wp_localize_script( $full_handle, $localization_object ?? $handle, $localization_args );
                wp_enqueue_script( $full_handle );
                $type    = 'script';
                $inlines = MC_Scripts::inlines()[ $full_handle ] ?? false;
            }
            $child  = true;
            $deps[] = $full_handle;
            
            if( !$inlines || empty( $type ) ) continue;
            foreach( $inlines as $inline ) {
                if( MC_Vars::stringContains( $inline, '.css' ) || MC_Vars::stringContains( $inline, '.js' ) ) {
                    $inline = MC_Url::urlToPath( $inline ) ?? '';
                    $inline = file_get_contents( $inline );
                    if( empty( $inline ) ) continue;
                }
                'wp_add_inline_'.$type( $full_handle, $inline );
            }
        }
    }
    
    /**
     * Enqueues files
     */
    public function enqueues() {
        MC_Scripts::jquery();
        $enqueues = [
            'style'  => apply_filters( 'mc_styles', MC_Styles::files() ),
            'script' => apply_filters( 'mc_scripts', MC_Scripts::files() )
        ];
        foreach( $enqueues as $type => $enqueue ) {
            foreach( $enqueue as $file ) {
                $condition = $file['condition'] ?? true;
                if( empty( $condition ) ) continue;
                $file['type'] = $type;
                self::enqueue( $file );
            }
        }
    }
    
    /**
     * Enqueues admin files
     */
    public function adminEnqueues() {
        //		MC_Scripts::jquery();
        $enqueues = [
            'script' => apply_filters( 'mc_admin_scripts', MC_Scripts::adminFiles() )
        ];
        foreach( $enqueues as $type => $enqueue ) {
            foreach( $enqueue as $file ) {
                $condition = $file['condition'] ?? true;
                if( empty( $condition ) ) continue;
                $file['type'] = $type;
                self::enqueue( $file );
            }
        }
    }
    
    /**
     * Add inline files
     */
    public function inlineChildFiles() {
        $file_types = [ 'css', 'js' ];
        foreach( $file_types as $file_type ) {
            if( !file_exists( $dir = MC_CHILD_DIR.'/src/.'.$file_type.'/inlines' ) ) continue;
            $files = MC_Files::scanDir( $dir );
            foreach( $files as $file ) {
                $file_path     = $dir.'/'.$file;
                $handle        = str_replace( '.'.$file_type, '', $file );
                $file_contents = file_get_contents( $file_path );
                if( $file_type == 'css' ) {
                    wp_add_inline_style( $handle, $file_contents );
                } else if( $file_type == 'js' ) {
                    wp_add_inline_script( $handle, $file_contents );
                }
            }
        }
    }
    
    /**
     * @param string $type
     *
     * @return string
     */
    public static function enqueueType( string $type = '' ) : string {
        $type = !empty( $type ) && ( $type == 'css' || $type == 'style' ) ? 'style' : $type;
        return !empty( $type ) && ( $type == 'js' || $type == 'script' ) ? 'script' : $type;
    }
    
    /**
     * @param string $prefix
     * @param bool   $child
     *
     * @return string
     */
    public static function prefixer( string $prefix = '', bool $child = false ) : string {
        $prefix = !empty( $prefix ) ? $prefix : 'mc-';
        $prefix = strtolower( $prefix );
        $prefix = MC_Vars::alphanumericOnly( $prefix );
        $child  = $child ? 'child-' : '';
        return $prefix.'-'.$child;
    }
    
    /**
     * @param string $handle
     * @param string $prefix
     * @param false  $child
     *
     * @return string
     */
    public static function handler( string $handle = '', string $prefix = '', bool $child = false ) : string {
        if( empty( $handle ) ) return '';
        $prefix = self::prefixer( $prefix, $child );
        $handle = str_replace( $prefix, '', $handle );
        return $prefix.$handle;
    }
    
    /**
     * @param string $url
     *
     * @return array
     */
    public static function linker( string $url = '' ) : array {
        $site    = MC_SITE;
        $results = [ 'url' => $url, 'path' => '' ];
        if( strpos( $url, $site ) === false && strpos( $url, '/' ) !== 0 ) return $results;
        $url             = str_replace( get_site_url(), '', $url );
        $url             = '/'.$url;
        $path            = ABSPATH.$url;
        $results['url']  = str_replace( '//', '/', $url );
        $results['path'] = str_replace( '//', '/', $path );
        return $results;
    }
    
}