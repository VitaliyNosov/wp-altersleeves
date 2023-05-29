<?php

namespace Mythic_Core\System;

use Mythic_Core\Utils\MC_Vars;
use WP_Post;
use WP_Post_Type;
use WP_Query;

/**
 * Class MC_Wordpress
 *
 * @package Mythic_Core\Utils
 */
class MC_WP {
    
    /**
     * @return array
     */
    public static function getPostTypeNames() : array {
        $post_types = self::getPostTypes( 'names' );
        if( function_exists( 'WC' ) ) {
            $post_types['shop_coupon'] = 'shop_coupon';
            $post_types['shop_order']  = 'shop_order';
        }
        
        return $post_types;
    }
    
    /**
     * @return array
     */
    public static function getPostTypes( $output = 'objects' ) : array {
        $args = [
            'public'   => true,
            '_builtin' => true,
        ];
        
        $operator = 'or'; // 'and' or 'or'
        
        return get_post_types( $args, $output, $operator );
    }
    
    /**
     * @return string
     */
    public static function uploadDir() : string {
        return wp_upload_dir()['path'];
    }
    
    /**
     * @return string
     */
    public static function uploadUri() : string {
        return wp_upload_dir()['url'];
    }
    
    /**
     * @param bool  $get
     * @param array $params
     *
     * @return int[]|WP_Post[]|WP_Query
     */
    public static function comments( $get = true, $params = [] ) {
        $args = [
            'post_type'      => 'comment',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        return $get ? get_posts( $args ) : new WP_Query( $args );
    }
    
    /**
     * @param bool  $get
     * @param array $params
     *
     * @return int[]|WP_Post[]|WP_Query
     */
    public static function posts( $get = true, $params = [] ) {
        $args = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        return $get ? get_posts( $args ) : new WP_Query( $args );
    }
    
    /**
     * @param bool  $get
     * @param array $params
     *
     * @return int[]|WP_Post[]|WP_Query
     */
    public static function pages( $get = true, $params = [] ) {
        $args = [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        return $get ? get_posts( $args ) : new WP_Query( $args );
    }
    
    /**
     * @param int $id
     *
     * @return string
     */
    public static function authorName( $id = 0 ) : string {
        if( empty( $id ) ) {
            $id = self::currentId();
            if( empty( $id ) ) return '';
        }
        $id = self::authorId( $id );
        
        return get_the_author_meta( 'display_name', $id ) ?? '';
    }
    
    /**
     * @return false|int
     */
    public static function currentId() {
        $object = get_queried_object();
        if( !is_object( $object ) || empty( $object ) ) {
            $id = get_the_ID();
            if( !empty( $id ) ) return $id;
            
            return 0;
        }
        
        return $object->ID;
    }
    
    /**
     * @param int $id
     *
     * @return int
     */
    public static function authorId( $id = 0 ) : int {
        if( empty( $id ) ) {
            $id = self::currentId();
            if( empty( $id ) ) return 0;
        }
        $post = get_post( $id );
        if( empty( $post ) ) return 0;
        
        return $post->post_author;
    }
    
    /**
     * @param int $id
     *
     * @return string
     */
    public static function exists( $id = 0 ) : string {
        if( !empty( get_post( $id ) ) ) return get_post_type( $id );
        if( !empty( get_user_by( 'ID', $id ) ) ) return 'user';
        
        return '';
    }
    
    /**
     * @param string $key
     * @param int    $id
     * @param string $type
     *
     * @return array|mixed
     */
    public static function meta( $key = 'all', $id = 0, $type = '' ) {
        if( empty( $id ) ) $id = get_the_ID();
        
        if( empty( $type ) && !empty( get_post( $id ) ) ) {
            $type = 'post';
        } else if( ( empty( $type ) || $type == 'user' ) && !empty( get_user_by( 'id', $id ) ) ) {
            $type = 'user';
        } else {
            $type = 'post';
        }
        
        if( $type == 'user' ) {
            if( !empty( $key ) && $key !== 'all' ) return get_user_meta( $id, $key, true );
            $meta_values = get_user_meta( $id );
        } else {
            if( !empty( $key ) && $key !== 'all' ) return get_post_meta( $id, $key, true );
            $meta_values = get_post_meta( $id );
        }
        
        if( empty( $meta_values ) ) return $meta_values;
        
        foreach( $meta_values as $key => $meta_value ) {
            if( empty( $meta_value ) ) continue;
            if( count( $meta_value ) == 1 ) $meta_value = $meta_value[0];
            $meta_value          = is_serialized( $meta_value ) ? unserialize( $meta_value ) : $meta_value;
            $meta_values[ $key ] = $meta_value;
        }
        
        return $meta_values;
    }
    
    /**
     * @param string $output
     * @param array  $args
     *
     * @return string[]|WP_Post_Type[]
     */
    public static function postTypes( $output = 'names', $args = [] ) : array {
        $output = MC_Vars::parseableString( $output );
        if( $output != 'names' ) $output = 'objects';
        
        return get_post_types( $args, $output );
    }
    
}
