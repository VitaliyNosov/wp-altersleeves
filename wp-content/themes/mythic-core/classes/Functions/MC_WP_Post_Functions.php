<?php

namespace Mythic_Core\Functions;

use WP_Post;

class MC_WP_Post_Functions {
    
    /**
     * @param int $blog
     * @param int $post_id
     *
     * @return array|object|WP_Post
     */
    public static function getPost( int $post_id = 0, int $blog = 0 ) {
        global $wpdb;
        $table = $blog < 2 ? 'wp_posts' : 'wp_'.$blog.'_posts';
        echo $wpdb->prepare( "SELECT * FROM {$table} WHERE ID = %d LIMIT 1", $post_id );
        $_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE ID = %d LIMIT 1", $post_id ) );
        return sanitize_post( $_post, 'raw' );
    }
    
    /**
     * @param int    $post_id
     * @param string $key
     * @param int    $blog
     *
     * @return array|object|string|null
     */
    public static function getPostMeta( int $post_id = 0, string $key = '', int $blog = 0 ) {
        global $wpdb;
        $table = $blog < 2 ? 'wp_postmeta' : 'wp_'.$blog.'_postmeta';
        $query = "SELECT meta_value FROM {$table} WHERE post_id ='{$post_id}'";
        if( empty( $key ) ) return $wpdb->get_results( $query );
        if( $key == OBJECT || $key == ARRAY_A ) return $wpdb->get_results( $query, $key );
        return $wpdb->get_var( $query." AND meta_key='{$key}'" );
    }
    
    /**
     * @param bool  $title
     * @param bool  $content
     * @param false $date
     */
    public static function defaultLoop( bool $content = true, bool $title = true, bool $date = false ) {
        if( have_posts() ) {
            while( have_posts() ) {
                the_post();
                switch( $content ) {
                    case  file_exists( $content ) :
                        include $content;
                        break;
                    case is_string( $content ) :
                        echo $content;
                        break;
                    default :
                        self::defaultLoopContent( $content, $title, $date );
                }
            }
        }
    }
    
    /**
     * @param bool $content
     * @param bool $title
     * @param bool $date
     */
    public static function defaultLoopContent( bool $content = true, bool $title = true, bool $date = false ) {
        if( !empty( $title ) ) the_title( '<h1>', '</h1>' );
        if( !empty( $date ) ) the_date();
        if( !empty( $content ) ) {
            if( function_exists( 'is_product' ) && is_product() ) {
                wc_get_template_part( 'content', 'single-product' );
            } else {
                the_content();
            }
        }
    }
    
}