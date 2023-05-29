<?php

namespace Mythic_Core\System;

/**
 * Class MC_Sidebars
 *
 * @package Mythic_Core\System
 */
class MC_Sidebars {
    
    /**
     * MC_Sidebars constructor.
     */
    public function __construct() {
        add_action( 'widgets_init', [ $this, 'registerSidebars' ] );
    }
    
    public function registerSidebars() {
        $this->home();
        $this->sidebar();
        $this->store();
        $this->footerSidebars();
    }
    
    public function home() {
        register_sidebar( [
                              'name'           => 'Home',
                              'id'             => 'home-widgets',
                              'before_sidebar' => '',
                              'after_sidebar'  => '',
                              'before_widget'  => '<div class="widget">',
                              'after_widget'   => '</div>',
                              'before_title'   => '<h2 class="heading">',
                              'after_title'    => '</h2>',
                          ] );
    }
    
    public function sidebar() {
        register_sidebar( [
                              'name'           => 'General Sidebar',
                              'id'             => 'sidebar',
                              'before_sidebar' => '<aside class="sidebar">',
                              'after_sidebar'  => '</aside>',
                              'before_widget'  => '<div class="widget">',
                              'after_widget'   => '</div>',
                              'before_title'   => '<h3 class="heading">',
                              'after_title'    => '</h3>',
                          ] );
    }
    
    public function store() {
        register_sidebar( [
                              'name'           => 'Store Filter',
                              'id'             => 'store-filter',
                              'before_sidebar' => '<aside class="sidebar col-lg-3 p-3 mb-3">
<h3 class="d-none d-sm-block">Results Filter</h3>
  <a class="d-sm-none"  data-bs-toggle="collapse" data-bs-target=".store-filter-hide" href="#storeFilter" aria-expanded="false" aria-controls="storeFilter storeFilterHide">
    Store Filter <span id="storeFilterHide" class="store-filter-hide collapse brand-color fw-bold">- click to close</span>
  </a>
<div id="storeFilter" class="store-filter-hide d-sm-block collapse">

',
                              'after_sidebar'  => '</div></aside>',
                              'before_widget'  => '<div class="widget">',
                              'after_widget'   => '</div>',
                              'before_title'   => '<h3 class="heading">',
                              'after_title'    => '</h3>',
                          ] );
    }
    
    public function footerSidebars() {
        for( $x = 1; $x <= 4; $x++ ) {
            $order = $x + 1;
            register_sidebar( [
                                  'name'           => 'Footer Nav '.$x,
                                  'id'             => 'footer-nav-'.$x,
                                  'before_sidebar' => '<div class="footer-nav col-sm order-'.$order.'">',
                                  'after_sidebar'  => '</div>',
                                  'before_widget'  => '<div class="widget">',
                                  'after_widget'   => '</div>',
                                  'before_title'   => '<h3 class="heading">',
                                  'after_title'    => '</h3>',
                              ] );
        }
    }
    
}