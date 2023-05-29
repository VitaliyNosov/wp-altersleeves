<?php

namespace Mythic\Functions\Wordpress;

use Mythic\Helpers\MC2_DB;
use WP_Post;

class MC2_Post_Functions {
    
    /**
     * @param int $blog
     * @param int $post_id
     *
     * @return array|object|WP_Post
     */
    public static function get( int $post_id = 0, int $blog = 0 ) {
        global $wpdb;
        $table = $blog < 2 ? 'posts' : $blog.'_posts';
        $table = $wpdb->prefix.$table;
        
        
        
        $wpdb->prepare( "SELECT * FROM {$table} WHERE ID = %d LIMIT 1", $post_id );
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
    public static function get_meta( int $post_id = 0, string $key = '', int $blog = 0 ) {
        global $wpdb;
        $table = $blog < 2 ? 'postmeta' : $blog.'_postmeta';
        $table = $wpdb->prefix.$table;
        $where = [ 'post_id' => $post_id];
        if( !empty($key) ) $where['meta_key'] = $key;
        return MC2_DB::get_var($table, $where );
    }
    
}