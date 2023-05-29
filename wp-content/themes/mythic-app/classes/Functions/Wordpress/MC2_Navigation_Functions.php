<?php

namespace Mythic\Functions\Wordpress;

use Mythic\Abstracts\MC2_Class;

class MC2_Navigation_Functions extends MC2_Class {
    
    public function actions() {
        add_action( 'after_setup_theme', [ $this, 'register_navs'] );
    }
    
    /**
     * Register all the navigation locations
     */
    public function register_navs() {
        register_nav_menu( 'header', 'Header Navigation' );
        register_nav_menu( 'dashboard', 'Dashboard Navigation' );
        register_nav_menu( 'footer_1', 'Footer 1st Navigation' );
        register_nav_menu( 'footer_2', 'Footer 2nd Navigation' );
        register_nav_menu( 'footer_3', 'Footer 3rd Navigation' );
        register_nav_menu( 'footer_social', 'Dashboard Navigation' );
    }
    
    /**
     * @param mixed $nav_id
     * @param bool   $test_if_empty
     *
     * @return array|false|\string[][]
     */
    public static function get_nav_items_for_front( $nav_id = '', bool $test_if_empty = true ) {
        if( !is_numeric($nav_id) ) {
            $menus = get_nav_menu_locations();
            $nav_id = $menus[$nav_id] ?? 0;
        }
        $items = wp_get_nav_menu_items($nav_id);
        if( empty($items) && $test_if_empty ) return self::test_navigation();
        foreach( $items as $key => $item ) {
            $parsed = [
                'text' => $item->post_title,
                'link' => $item->url,
                'classes' => ''
            ];
            unset($items[$key]);
            $items[$key] = $parsed;
        }
        return $items;
    }
    
    
    /**
     * @return string[][]
     */
    public static function test_navigation() : array {
        return [
            0 =>
                [
                    'text'    => 'Item 1',
                    'link'    => '#',
                    'classes' => '',
                ],
            1 =>
                [
                    'text'    => 'Item 2',
                    'link'    => '#',
                    'classes' => '',
                ],
            2 =>
                [
                    'text'    => 'Item 3',
                    'link'    => '#',
                    'classes' => '',
                ],
        ];
    }
    
}