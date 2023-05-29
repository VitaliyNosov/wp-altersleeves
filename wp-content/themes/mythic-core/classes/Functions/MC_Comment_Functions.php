<?php

namespace Mythic_Core\Functions;


class MC_Comment_Functions {
    
    public static function removePostTypeSupport() {
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
    
    public static function removeEditPage() {
        remove_menu_page( 'edit-comments.php' );
    }
    
}