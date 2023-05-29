<?php

namespace Mythic_Core\Display;

use MC_Vars;

/**
 * Class MC_Template_Parts
 *
 * @package Mythic_Core\Display
 */
class MC_Template_Parts {
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function adminField( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'admin/field-'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function get( $slug = '', $name = '', $args = [] ) : string {
        if( empty( $slug ) ) return '';
        
        $dirs       = [
            get_stylesheet_directory(),
            get_template_directory(),
        ];
        $connectors = [ '/', '-', '_' ];
        $count      = 0;
        foreach( $dirs as $dir ) {
            $count++;
            $full_path = $dir.'/'.'template-parts/'.$slug;
            if( empty( $name ) ) {
                if( !MC_Vars::stringContains( $full_path, '.php' ) ) $full_path .= '.php';
                if( file_exists( $full_path ) ) break;
                if( !file_exists( $full_path ) && $count == count( $dirs ) ) return '';
            } else {
                if( strpos( $name, '.php' ) === false ) $name .= '.php';
                foreach( $connectors as $connector ) {
                    $test_path = $full_path.$connector.$name;
                    if( file_exists( $test_path ) ) {
                        $full_path = $test_path;
                        break 2;
                    }
                }
            }
        }
        
        if( !file_exists( $full_path ) ) return '';
        
        if( !empty( $args ) && is_array( $args ) ) extract( $args );
        ob_start();
        include $full_path;
        return ob_get_clean() ?? '';
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function adminPage( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'admin/page-'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function component( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'components/'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     *
     * @return string
     */
    public static function content( $slug = '', $args = [] ) : string {
        return self::get( 'content/'.$slug, '', $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function form( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'forms/'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function formField( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'form-fields/'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function item( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'items/'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     *
     * @return string
     */
    public static function legal( $slug = '', $name = '', $args = [] ) : string {
        return self::get( 'legal/'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     *
     * @return string
     */
    public static function layout( $slug = '', $args = [] ) : string {
        return self::get( 'layouts/'.$slug, '', $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     *
     * @return string
     */
    public static function tool( $slug = '', $args = [] ) : string {
        return self::get( 'tools/'.$slug, '', $args );
    }
    
    /**
     * @param string $content
     * @param mixed  $classes
     * @param string $id
     *
     * @return string
     */
    public static function row( $content = '', $classes = '', $id = '' ) : string {
        if( is_array( $classes ) ) $classes = implode( ', ', $classes );
        if( !empty( $id ) ) $id = ' id="'.$id.'"';
        if( !is_string( $classes ) ) $classes = '';
        if( !empty( $classes ) ) $classes = ' '.$classes.'';
        
        return '<div'.$id.' class="row '.$classes.'">'.$content.'</div>';
    }
    
}
