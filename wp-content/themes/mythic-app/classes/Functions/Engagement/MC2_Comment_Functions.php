<?php

namespace Mythic\Functions\Engagement;

use Mythic\Abstracts\MC2_Class;

class MC2_Comment_Functions extends MC2_Class {

    public function actions() {
        add_action( 'admin_init', [ $this, 'remove_wp_support' ] );
        add_action( 'admin_menu', [ $this, 'remove_edit_page' ] );
    }
    
    /**
     * Removes wp support for comments as post type
     */
    public function remove_wp_support() {
        global $pagenow;
        if( $pagenow === 'edit-comments.php' ) {
            wp_redirect( admin_url() );
            exit;
        }
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        foreach( get_post_types() as $post_type ) {
            if( post_type_supports( $post_type, 'comments' ) ) {
                remove_post_type_support( $post_type, 'comments' );
                remove_post_type_support( $post_type, 'trackbacks' );
            }
        }
    }
    
    /**
     * Removes the edit comments page for comments as custom post type
     */
    public function remove_edit_page() {
        remove_menu_page( 'edit-comments.php' );
    }

}