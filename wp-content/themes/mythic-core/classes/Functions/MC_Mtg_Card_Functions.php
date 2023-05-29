<?php

namespace Mythic_Core\Functions;

use MC_Assets;
use MC_Images;
use MC_Mtg_Card;
use MC_Mtg_Printing;
use MC_Render;
use MC_Search_Functions;
use MC_Vars;
use Mythic_Core\Abstracts\MC_Tax_Functions;
use Mythic_Core\Objects\MC_Mtg_Set;
use WP_Error;
use WP_Post;
use WP_Term;

/**
 * Class MC_Mtg_Card_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Mtg_Card_Functions extends MC_Tax_Functions {
    
    public static $tax = 'mtg_card';
    
    /**
     * @param null  $card
     * @param array $params
     *
     * @return array
     */
    public static function printingIds( $card = null, $params = [] ) : array {
        if( empty( $card ) ) return [];
        $card_id = is_object( $card ) ? $card->id : $card;
        $args    = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'mtg_card',
                    'field'    => 'term_id',
                    'terms'    => $card_id,
                ],
                [
                    'taxonomy' => 'mtg_set',
                    'field'    => 'term_id',
                    'terms'    => MC_Mtg_Set::availableId()
                ]
            ],
            'meta_query'     => [
                'relation'         => 'AND',
                'release_date'     => [
                    'key' => 'mc_released_at',
                ],
                'collector_number' => [
                    'key' => 'mc_collector_number',
                ],
            ],
            'orderby'        => [
                'release_date'     => 'DESC',
                'collector_number' => 'ASC',
            ],
            'fields'         => 'ids',
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        return get_posts( $args );
    }
    
    /**
     * @param null $card
     * @param null $set
     *
     * @return array|mixed
     */
    public static function printingsBySet( $card = null, $set = null ) {
        $printing_ids = self::printingIdsBySet( $card, $set );
        foreach( $printing_ids as $key => $printing_id ) {
            $printing_ids[ $key ] = new MC_Mtg_Printing( $printing_id );
        }
        
        return $printing_ids;
    }
    
    /**
     * @param null $card
     * @param null $set
     *
     * @return array
     */
    public static function printingIdsBySet( $card = null, $set = null ) : array {
        if( empty( $card ) ) return [];
        $card_id = is_object( $card ) ? $card->id : $card;
        $set_id  = is_object( $set ) ? $set->id : $set;
        $args    = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'mtg_card',
                    'field'    => 'term_id',
                    'terms'    => $card_id,
                ],
                [
                    'taxonomy' => 'mtg_set',
                    'field'    => 'term_id',
                    'terms'    => $set_id,
                ],
            ],
            'fields'         => 'ids',
        ];
        
        return get_posts( $args );
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function unavailableCardImg( $type = 'jpg' ) : string {
        if( strtolower( $type ) != 'png' ) $type = 'jpg';
        
        return MC_Assets::getImgUrl( 'view/unavailable-card.'.$type );
    }
    


    
    /**
     * @return string
     */
    public static function generatePassPhrase() : string {
        $printings = MC_Mtg_Printing_Functions::query( [ 'orderby' => 'rand', 'posts_per_page' => 20 ] );
        
        $selected_words = [];
        $count = 0;
        while( $count <= 6) {
            $count = count($selected_words);
            foreach( $printings as $printing_id ) {
                $printing = new MC_Mtg_Printing( $printing_id );
                $name     = $printing->name;
                $words    = explode( ' ', $name );
                foreach( $words as $key => $word ) {
                    $word        = MC_Vars::alphanumericOnly( $word );
                    $parsed_word = MC_Vars::parseableString( $words );
                    if( empty( $word ) || $parsed_word == 'the' || $parsed_word == 'and' || $parsed_word == 'of' ) {
                        unset( $words[ $key ] );
                        continue;
                    }
                    $selected_words[ $key ] = $word;
                    $selected_words[]       = $word;
                }
            }
            $selected_words = array_unique( $selected_words );
        }
    
        shuffle( $selected_words );
        return $selected_words[0].$selected_words[1];
    }
    
    /**
     * @param string $search_term
     *
     * @return array|int|WP_Error|WP_Term[]
     */
    public static function search( $search_term = '', $params = [] ) {
        if( empty( $search_term ) ) return [];
        $search_term = MC_Vars::parseableString( $search_term, true );
        if( $search_term == 'x' ) {
            $card = get_term_by( 'id', 5109, 'mtg_card' );
            
            return [ $card ];
        }
        
        return self::queryBySearchableName( $search_term, $params );
    }
    
    /**
     * @param string $search_term
     * @param array  $params
     *
     * @return array|int|WP_Error|WP_Term[]
     */
    public static function queryBySearchableName( $search_term = '', $params = [] ) {
        if( empty( $search_term ) ) return [];
        $search_term = MC_Vars::alphanumericOnly( $search_term, true );
        
        $params['meta_query'] = [
            'searchable_name' => [
                'key'     => 'mc_searchable_name',
                'value'   => $search_term,
                'compare' => 'LIKE',
            ],
        ];
        if( !isset( $params['number'] ) ) $params['number'] = 12;
        
        $params['orderby']  = 'meta_value_num';
        $params['order']    = 'ASC';
        $params['meta_key'] = 'mc_edhrec_rank';
        
        return self::query( $params );
    }
    
    /**
     * @param array $params
     *
     * @return int|WP_Error|WP_Term[]
     */
    public static function query( $params = [] ) {
        $cards = parent::query( $params );
        
        return static::prepareSearchResult( $cards );
    }
    
    /**
     * @param $results
     *
     * @return array
     */
    public static function prepareSearchResult( $results ) {
        if( empty( $results ) ) return [];
        foreach( $results as $key => $card ) {
            $results[ $key ] = new MC_Mtg_Card( $card );
        }
        
        return $results;
    }
    
    /**
     * @param string $search_term
     * @param array  $params
     *
     * @return int|WP_Error|WP_Term[]
     */
    public static function printingsBySearchableName( $search_term = '', $params = [] ) {
        if( empty( $search_term ) ) return [];
        $cards     = self::queryBySearchableName( $search_term, $params );
        $printings = [];
        foreach( $cards as $card ) {
            $printings[] = self::printing( $card );
        }
        
        return $printings;
    }
    
    /**
     * @param null $card
     *
     * @return int|WP_Post
     */
    public static function printing( $card = null ) {
        $printings = self::printings( $card );
        
        return !empty( $printings ) ? $printings[0] : null;
    }
    
    /**
     * @param null  $card
     * @param array $params
     *
     * @return array
     */
    public static function printings( $card = null, $params = [] ) : array {
        $printing_ids = self::printingIds( $card, $params );
        foreach( $printing_ids as $key => $printing_id ) {
            $printing_ids[ $key ] = new MC_Mtg_Printing( $printing_id );
        }
        
        return $printing_ids;
    }
    
    /**
     * @param string $search_term
     * @param array  $params
     *
     * @return int|WP_Error|WP_Term[]
     */
    public static function queryForAutocomplete( $search_term = '' ) {
        return static::prepareSearchResult(
            MC_Search_Functions::searchWithIndexing( $search_term, 'card', 10, 0, 1 )
        );
    }
    
    /**
     * @param array $args
     */
    public static function render( $args = [] ) {
        MC_Render::item( 'card', '', $args );
    }
    
    /**
     * @param string $name
     *
     * @return int
     */
    public static function id( $name = '' ) {
        $card = get_term_by( 'name', $name, 'mtg_card' );
        if( !empty( $card ) ) return $card->term_id;
        $name = str_replace( "’", "'", $name );
        $card = wp_insert_term( $name, 'mtg_card', [
            'slug' => sanitize_title( $name ),
        ] );
        return $card['term_id'];
    }
    
    public static function getDisallowedCardIds() {
        $cards = self::getDisallowedCardNames();
        $ids   = [];
        foreach( $cards as $card ) {
            $id = get_term_by( 'name', $card, 'mtg_card' );
            if( $id == null ) continue;
            $ids[] = $id->term_id;
        }
        
        return $ids;
    }
    
    /**
     * @return string[]
     */
    public static function getDisallowedCardNames() {
        return [ 'The Great Forest', 'Turri Island' ];
    }
    

    /**
     * @return array
     */
    public static function getStandardQueryArgs() {
        return [
            'taxonomy'   => 'mtg_card', // taxonomy name
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => true, // @Todo add inactive cards
            'fields'     => 'all',
            'exclude'    => self::getDisallowedCardIds(),
        ];
    }
    
    /**
     * @param int   $card_id
     * @param false $generic
     *
     * @return array
     */
    public static function printings_for_submission( $card_id = 0, $generic = false ) : array {
        $card = get_term_by( 'term_id', $card_id, 'mtg_card' );
        if( empty( $card ) ) return [];
        $land = self::is_basic_land( $card_id );
        
        $unavailable = get_term_by( 'name', 'Unavailable', 'mtg_set' );
        if( $land && $generic ) {
            // To be finished
            return [];
        } else {
            $args = [
                'post_type'      => 'printing',
                'posts_per_page' => -1,
                'order'          => 'ASC',
                'tax_query'      => [
                    'relation' => 'AND',
                    [
                        'taxonomy' => 'mtg_card',
                        'field'    => 'term_id',
                        'terms'    => $card_id,
                    ],
                ],
                'fields'         => 'ids',
                'meta_query'     => [
                    'relation'         => 'AND',
                    'release_date'     => [
                        'key' => 'mc_released_at',
                    ],
                    'collector_number' => [
                        'key' => 'mc_collector_number',
                    ],
                ],
                'orderby'        => [
                    'release_date'     => 'DESC',
                    'collector_number' => 'ASC',
                ],
            ];
            
            if( !empty( $unavailable ) ) {
                $unavailable         = get_term_children( $unavailable->term_id, 'mtg_set' );
                $args['tax_query'][] = [
                    'taxonomy' => 'mtg_set',
                    'field'    => 'term_id',
                    'terms'    => $unavailable,
                    'operator' => 'NOT IN',
                ];
            }
            $printings = get_posts( $args );
            
            $results = [];
            foreach( $printings as $printing_id ) {
                $printing  = new MC_Mtg_Printing( $printing_id );
                $results[] = [
                    'id'        => $printing_id,
                    'img'       => $printing->imgJpgNormal,
                    'land'      => $land,
                    'name'      => utf8_encode( $printing->fullName ),
                    'framecode' => $printing->framecode_id,
                ];
            }
            
            return $results;
        }
    }
    
    /**
     * @param $card_id
     *
     * @return bool
     */
    public static function is_basic_land( $card_id ) : bool {
        $card = get_term_by( 'term_id', $card_id, 'mtg_card' );
        if( empty( $card ) ) return false;
        
        return self::is_basic_land_name( $card->name );
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public static function is_basic_land_name( $name = '' ) : bool {
        $name = trim( strtolower( $name ) );
        switch( $name ) {
            case 'forest' :
            case 'island' :
            case 'swamp' :
            case 'mountain' :
            case 'plains' :
                return true;
            default :
                return false;
        }
    }
    
    /**
     * @param string $search_term
     * @param int    $limit
     * @param int    $offset
     * @param bool   $with_link
     *
     * @return array
     */
    public static function searchCards( $search_term = '', $limit = 150, $offset = 0, $with_link = false ) : array {
        if( empty( $search_term ) ) return [];
        $search_term = trim( strtolower( MC_Vars::alphanumericOnly( $search_term ) ) );
        if( $search_term == 'x' ) {
            $card = get_term_by( 'id', 5109, 'mtg_card' );
            
            return [ $card ];
        }
        
        $args               = self::getStandardQueryArgs();
        $args['number']     = $limit;
        $args['offset']     = $offset;
        $args['meta_query'] = [
            [
                'key'     => 'mc_searchable_name',
                'value'   => $search_term,
                'compare' => 'LIKE',
            ],
        ];
        $cards              = get_terms( $args );
        
        if( empty( $cards ) ) return [];
        
        foreach( $cards as $card_key => $card ) {
            $results[ $card_key ] = [
                'id'   => $card->term_id,
                'name' => $card->name,
            ];
            if( $with_link ) {
                $results[ $card_key ]['link'] = '/browse?type=card&card_id='.$card->term_id;
            }
        }
        ksort( $results );
        
        return array_values( $results );
    }
    
}