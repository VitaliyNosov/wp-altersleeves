<?php

namespace Mythic_Core\Abstracts;

use Mythic_Core\System\MC_Statuses;
use WP_Post;

/**
 * Class MC_Post_Type_Functions
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Post_Type_Functions {
    
    public static $post_type = '';
    
    /**
     * @param array $params
     *
     * @return int[]|WP_Post[]
     */
    public static function query( $params = [], $all = false ) : array {
        $args = [
            'post_type'      => static::$post_type,
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [],
            'meta_query'     => [],
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        if( $all ) $args['post_status'] = MC_Statuses::keys();
        return get_posts( $args );
    }
    
}