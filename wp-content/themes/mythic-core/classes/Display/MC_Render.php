<?php

namespace Mythic_Core\Display;

/**
 * Class MC_Render
 *
 * @package Mythic_Core\Utils
 */
class MC_Render {
    
    /**
     * Outputs the website's breadcrumbs
     *
     * @param $args
     */
    public static function breadcrumbs( $args = [] ) {
        echo MC_Template_Parts::component( 'breadcrumbs', '', $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     */
    public static function content( string $slug = '', array $args = [] ) {
        if( empty( $slug ) ) $slug = apply_filters( 'mc_content_filter', 'default' );
        echo MC_Template_Parts::content( $slug, $args );
    }
    
    /**
     * @param string $content
     * @param mixed  $classes
     * @param string $id
     *
     */
    public static function row( $content = '', $classes = '', $id = '' ) {
        echo MC_Template_Parts::row( $content, $classes, $id );
    }
    
    /**
     * @param string $class
     */
    public static function loader( $class = '' ) {
        $args   = [ 'class' => $class ];
        $loader = MC_Template_Parts::get( 'loading', 'spinner', $args );
        echo $loader;
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function templatePart( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::get( $slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function adminField( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::adminField( $slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function adminPage( $slug = '', $args = [] ) {
        echo MC_Template_Parts::adminPage( $slug, '', $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function component( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::component( $slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     */
    public static function form( $slug = '', $args = [] ) {
        if( empty( $slug ) ) return;
        echo MC_Template_Parts::form( $slug, '', $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function formField( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::formField( $slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function item( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::item( $slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function itemCard( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::component( 'card'.$slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param string $name
     * @param array  $args
     */
    public static function legal( $slug = '', $name = '', $args = [] ) {
        echo MC_Template_Parts::legal( $slug, $name, $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     */
    public static function layout( string $slug = '', array $args = [] ) {
        if( empty( $slug ) ) $slug = apply_filters( 'mc_layout_filter', 'default' );
        echo MC_Template_Parts::layout( $slug, $args );
    }
    
    /**
     * @param string $slug
     * @param array  $args
     */
    public static function tool( $slug = '', $args = [] ) {
        if( empty( $slug ) ) $slug = apply_filters( 'mc_tools_filter', 'default' );
        echo MC_Template_Parts::tool( $slug, $args );
    }
    
}