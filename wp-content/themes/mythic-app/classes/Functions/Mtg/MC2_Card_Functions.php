<?php

namespace Mythic\Functions\Mtg;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Card_Functions
 *
 * @package Mythic\Functions\Mtg
 */
class MC2_Card_Functions extends MC2_DB_Table {

    protected static $table_name = 'mtg_cards';
    public static $tax = 'mtg_card';
    public static $name = 'M:TG Cards';

    /**
     * @return array
     */
    protected function get_parameters() : array {
        return [
            'key'   => self::$tax,
            'posts' => [ 'product' ],
            'label' => self::$name,
        ];
    }

    /**
     * @return string
     */
    function create_table_query() : string {
        $table_name = $this->get_table_name();
        return "CREATE TABLE `$table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `searchable_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `type_line` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
              `card_type` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
              `edhrec_rank` mediumint(3) unsigned NOT NULL DEFAULT '0',
              `last_edited` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `edhrec_rank` (`edhrec_rank`),
              KEY `name` (`name`),
              KEY `searchable_name` (`searchable_name`),
              KEY `type_line` (`type_line`),
              KEY `card_type` (`card_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * @param int $card_id
     *
     * @return array|object|void|null
     */
    public static function get_by_id( $card_id = 0 ) {
        global $wpdb;
        $table_name = self::get_card_table_name();
        return $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $card_id" );
    }

    /**
     * @param int $scryfall_id
     *
     * @return array|object|void|null
     */
    public static function get_by_scryfall_id( $scryfall_id = 0 ) {
        global $wpdb;
        $table_name = self::get_card_table_name();
        return $wpdb->get_row( "SELECT * FROM $table_name WHERE scryfall_id = '$scryfall_id'" );
    }

    /**
     * @param int $name
     *
     * @return array|object|void|null
     */
    public static function get_by_name( $name = 0 ) {
        global $wpdb;
        $table_name = self::get_card_table_name();
        $name       = self::make_term_searchable( $name );
        return $wpdb->get_row( "SELECT * FROM $table_name WHERE searchable_name LIKE '%$name%'" );
    }

    /**
     *
     * Searches cards via the searchable name
     *
     * @param string $term
     *
     * @return array|object|null
     */
    public static function search_cards_by_name( $term = '', $limit = 150, $offset = 0 ) {
        global $wpdb;
        $term       = trim( strtolower( MC2_Vars::alphanumericOnly( $term ) ) );
        $table_name = self::get_card_table_name();
        return $wpdb->get_results( "SELECT TOP $limit * FROM $table_name WHERE searchable_name LIKE '$term' OFFSET $offset ORDER BY edhrec_rank DESC" );
    }

    /**
     *
     * The default image to be used for cards if the main image is not available
     *
     * @param string $type
     *
     * @return string
     */
    public static function default_image( $type = 'jpg' ) : string {
        return MC2_Assets::getImgUrl( 'view/unavailable-card.'.$type );
    }

    /**
     * Generates a phrase to be used for shortlinks or marketing material
     *
     * @return string
     */
    public static function generate_marketing_phrase() : string {
        $printings = self::get_all();
        shuffle( $printings );
        $words = [];
        foreach( $printings as $printing ) {
            $name  = $printing->name;
            $words = explode( ' ', $name );
            foreach( $words as $key => $word ) {
                $word        = MC2_Vars::alphanumericOnly( $word );
                $parsed_word = MC2_Vars::stringSafe( $words );
                if( empty( $word ) || $parsed_word == 'the' || $parsed_word == 'and' || $parsed_word == 'of' ) {
                    unset( $words[ $key ] );
                    continue;
                }
                $words[ $key ] = $word;
                $words[]       = $word;
            }
        }
        $words = array_unique( $words );
        shuffle( $words );
        if( count( $words ) < 2 ) return MC2_Vars::generate();
        return $words[0].$words[1];
    }

    /**
     * @param $results
     *
     * @return array
     */
    public static function prepareSearchResult( $results ) {
        if( empty( $results ) ) return [];
        foreach( $results as $key => $card ) {
            $results[ $key ] = new MC2_Mtg_Card( $card );
        }

        return $results;
    }

    /**
     * @param string $search_term
     * @param array  $params
     *
     * @return int|WP_Error|WP_Term[]
     */
    public static function queryForAutocomplete( $search_term = '' ) {
        return static::prepareSearchResult(
            MC2_Search_Functions::searchWithIndexing( $search_term, 'card', 10, 0, 1 )
        );
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
                $printing  = new MC2_Mtg_Printing( $printing_id );
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
     *
     * Checks if card is basic land or not
     *
     * @param $card
     *
     * @return bool
     */
    public static function is_basic_land( $card ) {
        if( is_numeric( $card ) ) $card = self::get_by_id( $card );
        if( empty( $card ) ) return false;
        return $card->type == 'land';
    }

    /**
     * Makes the term machine searchable for searches of card and set name
     *
     * @param string $term
     *
     * @return string
     */
    public static function make_term_searchable( $term = '' ) {
        return trim( strtolower( MC2_Vars::alphanumericOnly( $term ) ) );
    }

}