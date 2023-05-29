<?php

namespace Mythic_Core\Functions;

use MC_Mtg_Printing;
use Mythic_Core\Abstracts\MC_Tax_Functions;
use WP_Post;

/**
 * Class MC_Mtg_Set_Functions
 *
 * @package Mythic_Core\Objects
 */
class MC_Mtg_Set_Functions extends MC_Tax_Functions {
    
    public static $tax = 'mtg_set';
    
    /**
     * @param null $set
     *
     * @return array|int[]|WP_Post[]
     */
    public static function printingIds( $set = null ) : array {
        if( empty( $set ) ) return [];
        $set_id       = is_object( $set ) ? $set->id : $set;
        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'mtg_set',
                    'field'    => 'term_id',
                    'terms'    => $set_id,
                ],
            
            ],
            'fields'         => 'ids',
        ];
        
        return get_posts( $printingArgs );
    }
    
    /**
     * @param null $set
     *
     * @return int[]|WP_Post[]
     */
    public static function printings( $set = null ) : array {
        $printing_ids = self::printingIds( $set );
        foreach( $printing_ids as $key => $printing_id ) {
            $printing_ids[ $key ] = new MC_Mtg_Printing( $printing_id );
        }
        
        return $printing_ids;
    }
    
    
    /**
     * @param string $code
     *
     * @return int
     */
    public static function idByCode( $code = '' ) : int {
        $set = get_term_by( 'slug', $code, 'mtg_set' );
        if( empty($set) ) {
            $code = str_replace( "’", "'", $code );
            $set = wp_insert_term( $code, 'mtg_set', [
                'slug' => sanitize_title( $code ),
            ] );
            if( is_numeric($set) ) return $set;
            return 0;
        }
        
        return $set->term_id;
    }
    
    
}

