<?php

namespace Mythic_Core\System;

use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Admin_Bar
 *
 * @package Mythic_Core\System
 */
class MC_Admin_Bar {
    
    public static function init() {
        $class = __CLASS__;
        new $class();
    }
    
    public function __construct() {
        $this->clean();
    }
    
    public static function hide() {
        if( MC_User_Functions::isAdmin() ) return;
        show_admin_bar( false );
    }
    
    public static function clean() {
        global $current_user_id, $wp_admin_bar;
        $links = [
            'wp-logo',
            'appearance',
            'comments',
            'customize',
            'gforms_forms',
            'gform-forms',
            'my-account',
            'my-account-with-avatar',
            'my-blogs',
            'new-content',
            'new_draft',
            'updates',
            'wpseo-menu'
        ];
        if( $current_user_id != 1 ) $links[] = 'bar-query-monitor';
        foreach( $links as $link ) $wp_admin_bar->remove_menu( $link );
    }
    
    public static function addElements() {
        global $wp_admin_bar;
        
        $adminParents = [
            'admin'   => [],
            'objects' => [],
            'cutter'  => []
        ];
        
        foreach( $adminParents as $key => $adminParent ) {
            $wp_admin_bar->add_menu( [
                                         'id'    => $key,
                                         'title' => __( ucfirst( $key ) ),
                                     ] );
        }
        
        $adminParents['objects'][] = [
            'id'    => 'pages',
            'title' => __( 'Pages' ),
            'href'  => admin_url( '/edit.php?post_type=page' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'printings',
            'title' => __( 'Printings' ),
            'href'  => admin_url( '/edit.php?post_type=printing' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'products',
            'title' => __( 'Products' ),
            'href'  => admin_url( 'edit.php?post_type=product' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'coupons',
            'title' => __( 'Coupons' ),
            'href'  => admin_url( 'edit.php?post_type=shop_coupon' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'designs',
            'title' => __( 'Designs' ),
            'href'  => admin_url( 'edit.php?post_type=design&orderby=ID&order=desc' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'orders-all',
            'title' => __( 'All Orders' ),
            'href'  => admin_url( '/network/admin.php?page=woonet-woocommerce' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'orders-site',
            'title' => __( 'Site Orders' ),
            'href'  => admin_url( 'edit.php?post_type=shop_order&orderby=ID&order=desc' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'users',
            'title' => __( 'Users' ),
            'href'  => admin_url( 'users.php' ),
        ];
        
        $adminParents['objects'][] = [
            'id'    => 'backers',
            'title' => __( 'Backers' ),
            'href'  => admin_url( 'edit.php?post_type=backer' ),
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'partners',
            'title' => 'Affiliates',
            'href'  => '/dashboard/affiliate-control',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'approval',
            'title' => 'Approval',
            'href'  => '/approval',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'orders-overview-us',
            'title' => 'Process Orders (US)',
            'href'  => '/orders-overview?loc=us',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'orders-overview-nl',
            'title' => 'Process Orders (NL)',
            'href'  => '/orders-overview?loc=nl',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'orders-issues',
            'title' => 'Order Issues',
            'href'  => '/order-errors',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'mythic-frames',
            'title' => 'Mythic Frames',
            'href'  => '/mythic-frames',
        ];
    
        $adminParents['admin'][] = [
            'id'    => 'lgs',
            'title' => '#LGS Coupon Generator',
            'href'  => '/lgs-code-generator',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'order-notes',
            'title' => 'Fulfillment Log',
            'href'  => '/order-notes',
        ];
        
        $adminParents['admin'][] = [
            'id'    => 'withdrawal-review',
            'title' => 'Withdrawal Review',
            'href'  => '/withdrawal-review',
        ];
        
        $adminParents['cutter'][] = [
            'id'    => 'mask-maps',
            'title' => 'Mask Maps',
            'href'  => '/cutter-management?target=maps&role=list',
        ];
        
        $adminParents['cutter'][] = [
            'id'    => 'preconfigurations',
            'title' => 'Preconfigurations',
            'href'  => '/cutter-management?target=preconfigurations',
        ];
        
        $adminParents['cutter'][] = [
            'id'    => 'frame-elements',
            'title' => 'Frame Elements',
            'href'  => '/cutter-management?edit=elements',
        ];
        
        asort( $adminParents['objects'] );
        
        foreach( $adminParents as $adminKey => $adminItems ) {
            foreach( $adminItems as $adminItem ) {
                $wp_admin_bar->add_menu( [
                                             'parent' => $adminKey,
                                             'id'     => $adminItem['id'],
                                             'title'  => $adminItem['title'],
                                             'href'   => $adminItem['href'],
                                         ] );
            }
        }
        
        $wp_admin_bar->add_menu( [
                                     'id'    => 'mc-search',
                                     'title' => '<form role="search" method="get" action="'.MC_SITE.'" accept-charset="UTF-8"><input name="s" type="text" placeholder="Search any ID here" style="padding: 0 5px;border:0;" autocomplete="off"></form>',
                                 ] );
        
        $wp_admin_bar->add_menu( [
                                     'id'    => 'mc-search-order',
                                     'title' => '<form class="mx-2" role="search" method="get"  action="'.MC_SITE.'wp-admin/edit.php" accept-charset="UTF-8"><input name="s" type="text" placeholder="Any order field here" style="padding: 0 5px;border:0;" autocomplete="off">
            <input type="hidden" name="post_status" value="all">
            <input type="hidden" name="post_type" value="shop_order">
            <input type="hidden" name="action" value="-1">
            <input type="hidden" name="action2" value="-1">
            <input type="hidden" name="m" value="0">
            <input type="hidden" name="layout" value="">
                        <input type="hidden" name="_customer_user" value="">
            <input type="hidden" name="paged" value="1"></form>',
                                 ] );
        
        $wp_admin_bar->add_menu( [
                                     'id'    => 'mc-search-users',
                                     'title' => '<form class="mx-2"  role="search" method="get" action="'.MC_SITE.'wp-admin/users.php" accept-charset="UTF-8"><input name="s"type="text" placeholder="Any User field" style="padding: 0 5px;border:0;" autocomplete="off">
            <input type="hidden" name="new_role" value="">
            <input type="hidden" name="new_role2" value="">
            <input type="hidden" name="action" value="-1">
            <input type="hidden" name="action2" value="-1">
            <input type="hidden" name="layout" value="">
            <input type="hidden" name="paged" value="1"></form>',
                                 ] );
    }
    
}