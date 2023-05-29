<?php

namespace Mythic\Functions\Mtg;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Printing_Functions
 *
 * @package Mythic\Functions\Mtg
 */
class MC2_Printing_Functions extends MC2_DB_Table {

    protected static $table_name = 'mtg_printings';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `card_id` int(9) unsigned NOT NULL,
              `scryfall_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
              `face` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
              `set_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
              `set_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
              `collector_number` mediumint(3) unsigned NOT NULL,
              `lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN',
              `border_color` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'black',
              `frame` mediumint(3) unsigned NOT NULL DEFAULT '2015',
              `edhrec_rank` mediumint(3) unsigned NOT NULL DEFAULT '0',
              `last_edited` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `edhrec_rank` (`edhrec_rank`),
              KEY `collector_number` (`collector_number`),
              KEY `set_name` (`set_name`),
              KEY `set_code` (`set_code`),
              KEY `card_id` (`card_id`),
              KEY `scryfall_id` (`scryfall_id`),
              KEY `face` (`face`),
              KEY `lang` (`lang`),
              KEY `border_color` (`border_color`),
              KEY `frame` (`frame`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * @return string[]
     */
    public function meta_tables() : array {
        return [
            'mtg_printing_images',
        ];
    }

    /**
     * @return string[]
     */
    public function create_meta_table_queries() : array {
        return [
            'mtg_printing_images' =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `printing_id` int(11) unsigned NOT NULL,
                  `image_size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file` longtext COLLATE utf8mb4_unicode_ci,
                  `path` longtext COLLATE utf8mb4_unicode_ci,
                  `url` longtext COLLATE utf8mb4_unicode_ci,
                  PRIMARY KEY (`id`),
                  KEY `image_size` (`image_size`),
                  KEY `file_type` (`file_type`),
                  KEY `printing_id` (`printing_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        ];
    }

    const        NAME                   = 'printing';
    const        READABLE_NAME          = 'Printing';
    public const TYPE_LINE_CREATURE     = 'Creature';
    public const TYPE_LINE_NON_CREATURE = 'Non-Creature';
    public const TYPE_LINE_PLANESWALKER = 'Planeswalker';
    public static $post_type = 'printing';

    /**
     * @param int    $printing_id
     * @param string $card_name
     *
     * @return int
     */
    public static function getCardId( $printing_id = 0, string $card_name = '' ) : int {
        if( empty( $printing_id ) ) return 0;
        $cards = wp_get_object_terms( $printing_id, 'mtg_card' );
        if( !empty( $cards ) && is_array( $cards ) ) {
            $card = $cards[0];
        } else {
            if( !empty( $card_name ) ) {
                $card = get_term_by( 'name', $card_name, 'mtg_card' );
            }
        }
        if( empty( $card ) ) return 0;

        return $card->term_id;
    }

    /**
     * @param int $card_id
     *
     * @return array|int|WP_Post
     */
    public static function getSingleByCardId( $card_id = 0 ) : array {
        $printings = self::getByCardId( $card_id );
        if( empty( $printings ) || !is_array( $printings ) ) return 0;

        return $printings[0];
    }

    /**
     * @param int $card_id
     *
     * @return int[]|WP_Post[]
     */
    public static function getByCardId( $card_id = 0 ) : array {
        $params = [
            'tax_query' => [
                'taxonomy' => 'mtg_card',
                'field'    => 'term_id',
                'terms'    => $card_id,
            ],
        ];

        return self::query( $params );
    }

    /**
     * @param int $printing_id
     *
     * @return int
     */
    public static function setId( $printing_id = 0 ) : int {
        $set = self::set( $printing_id );
        if( empty( $set ) || !is_object( $set ) ) return 0;

        return $set->term_id;
    }

    /**
     * @param null $printing
     *
     * @return mixed|WP_Term|null
     */
    public static function set( $printing = null ) {
        if( empty( $printing ) ) return null;
        $printing_id = is_object( $printing ) ? $printing->id : $printing;
        $sets        = wp_get_object_terms( $printing_id, 'mtg_set' );
        if( !empty( $sets ) && is_array( $sets ) ) {
            return $sets[0];
        }

        return null;
    }

    /**
     * @param int $printing_id
     *
     * @return int
     */
    public static function getFrameCodeId( $printing_id = 0 ) : int {
        $framecode = self::getFrameCode( $printing_id );
        if( empty( $framecode ) || !is_object( $framecode ) ) return 0;

        return $framecode->term_id;
    }

    /**
     * @param null $printing
     *
     * @return mixed|WP_Term|null
     */
    public static function getFrameCode( $printing = null ) {
        if( empty( $printing ) ) return null;
        $printing_id = is_object( $printing ) ? $printing->id : $printing;
        $framecodes  = wp_get_object_terms( $printing_id, 'frame_code' );
        if( !empty( $framecodes ) && is_array( $framecodes ) ) return $framecodes[0];
        return null;
    }

    /**
     * @param null $printing
     *
     * @return mixed
     */
    public static function meta( $printing = null ) {
        $printing_id = is_object( $printing ) ? $printing->id : $printing;

        return get_post_meta( $printing_id );
    }

    /**
     * @param null $printing
     *
     * @return mixed|string
     */
    public static function prexistingImageSizes( $printing = null ) : string {
        $printing_id      = is_object( $printing ) ? $printing->id : $printing;
        $prexisting_sizes = MC2_WP::meta( 'mc_image_sizes', $printing_id );
        $prexisting_sizes = !empty( $prexisting_sizes ) ? $prexisting_sizes : [];
        foreach( $prexisting_sizes as $key => $prexisting_size ) {
            if( $prexisting_size == -1 ) unset( $prexisting_sizes[ $key ] );
            update_post_meta( $printing_id, 'mc_image_sizes', $prexisting_sizes );
        }

        return end( $prexisting_sizes );
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function codeFromId( $id = 0 ) {
        if( empty( $id ) ) return 0;

        $frameElements = wp_get_object_terms( $id, 'mtg_frame' );

        if( empty( $frameElements ) && get_post_type( $id ) == 'printing' ) self::frameSync( $id );
        if( empty( $frameElements ) || has_term( 'Universal', 'mtg_frame', $id ) ) return 'universal';
        $frameEffects = [];
        foreach( $frameElements as $key => $frameElement ) {
            $parent    = $frameElement->parent;
            $idElement = $frameElement->term_id;
            if( $parent == 24181 ) {
                $frameEffects[] = $idElement;
                unset( $frameElements[ $key ] );
            }
        }
        foreach( $frameElements as $key => $frameElement ) {
            $parent = $frameElement->parent;
            $parent = get_term_by( 'id', $parent, 'mtg_frame' );

            if( !empty( $parent ) ) {
                $parent    = $parent->name;
                $parent    = MC2_Vars::readableToKey( $parent );
                $idElement = $frameElement->term_id;
                if( $idElement == 24180 ) {
                    $idElement = $idElement.self::setCode( $id );
                }
                $frameElements[ $parent ] = $idElement;
            } else {
                $parent                   = MC2_Vars::readableToKey( $frameElement->name );
                $idElement                = $frameElement->term_id;
                $frameElements[ $parent ] = $idElement;
            }
            unset( $frameElements[ $key ] );
        }
        if( !empty( $frameEffects ) ) {
            $frameEffects                  = implode( '-', $frameEffects );
            $frameElements['frame_effect'] = $frameEffects;
        }
        if( empty( $frameElements ) ) return '';

        return self::codeFromElements( $frameElements );
    }

    /**
     * @param int $printing_id
     */
    public static function frameSync( $printing_id = 0 ) {
        $keys = [ 'type_line', 'layout', 'frame', 'frame_effects', 'rarity', 'full_art', 'border_color' ];
        wp_delete_object_term_relationships( $printing_id, 'mtg_frame' );
        foreach( $keys as $key ) {
            $meta = get_printing_meta( $printing_id, MC2_HOOK.$key, true );
            if( empty( $meta ) ) continue;
            switch( $key ) {
                case 'border_color' :
                    $tax = get_term_by( 'name', 'Borderless', 'mtg_frame' );
                    if( is_object( $tax ) && strtolower( $meta ) == 'borderless' ) {
                        $tax = $tax->term_id;
                        wp_set_post_terms( $printing_id, [ $tax ], 'mtg_frame', true );
                    }
                    break;
                case 'rarity' :
                    $foil_stamp = get_term_by( 'name', 'Foil Stamp', 'mtg_frame' );
                    $rarity     = get_term_by( 'slug', $meta, 'mtg_frame' );
                    if( is_object( $rarity ) ) {
                        wp_set_post_terms( $printing_id, [ 36068 ], 'mtg_frame', true );
                    }
                    $year = get_printing_meta( $printing_id, 'mc_frame', true );
                    $year = trim( $year );
                    if( $year != 2015 || ( $meta != 'mythic' && $meta != 'rare' ) ) {
                        break;
                    }
                    if( is_object( $foil_stamp ) ) {
                        $foil_stamp = $foil_stamp->term_id;
                        wp_set_post_terms( $printing_id, [ $foil_stamp ], 'mtg_frame', true );
                    }
                    break;
                case 'full_art' :
                    $tax = get_term_by( 'name', 'Full Art', 'mtg_frame' );
                    if( is_object( $tax ) ) {
                        $tax = $tax->term_id;
                        wp_set_post_terms( $printing_id, [ $tax ], 'mtg_frame', true );
                    }
                    break;
                case 'frame_effects' :
                    $frameEffects = is_array( $meta ) ? $meta : maybe_unserialize( $meta );
                    if( !is_array( $frameEffects ) ) $frameEffects = [ $frameEffects ];

                    foreach( $frameEffects as $effect ) {
                        $tax = get_term_by( 'slug', $effect, 'mtg_frame' );
                        if( is_object( $tax ) ) {
                            $tax = $tax->term_id;
                            wp_set_post_terms( $printing_id, [ $tax ], 'mtg_frame', true );
                        } else {
                            $tax = wp_insert_term( ucfirst( $effect ), 'mtg_frame', [
                                'name'   => $effect,
                                'parent' => 24181,
                            ] );
                            wp_set_post_terms( $printing_id, [ $tax['term_id'] ], 'mtg_frame', true );
                        }
                    }
                    break;
                default :
                    $meta = self::componentTranslator( $meta, $key );
                    $tax  = get_term_by( 'name', $meta, 'mtg_frame' );
                    if( is_object( $tax ) ) {
                        $tax = $tax->term_id;
                        wp_set_post_terms( $printing_id, [ $tax ], 'mtg_frame', true );
                    }
                    break;
            }
        }
        wp_delete_object_term_relationships( $printing_id, 'frame_code' );
        if( empty( wp_get_object_terms( $printing_id, 'mtg_frame' ) ) ) {
            $framecode = 'universal';
        } else {
            wp_delete_object_term_relationships( $printing_id, 'frame_code' );
            $framecode = self::codeFromId( $printing_id );
        }
        $objectFramecode = get_term_by( 'name', $framecode, 'frame_code' );
        if( empty( $objectFramecode ) ) {
            $framecode   = wp_insert_term( $framecode, 'frame_code' );
            $idFrameCode = $framecode['term_id'];
        } else {
            $idFrameCode = $objectFramecode->term_id;
        }
        wp_set_object_terms( $printing_id, [ (int) $idFrameCode ], 'frame_code' );
    }

    /**
     * @param string $term
     * @param string $type
     *
     * @return mixed|string
     */
    public static function componentTranslator( $term = '', $type = '' ) : string {
        if( empty( $term ) || empty( $type ) ) return '';

        switch( $type ) {
            case 'type_line' :
                $componentTranslations = self::typeLineTranslations();
                break;
            case 'layout' :
                $componentTranslations = self::layoutTranslations();
                break;
            case 'frame' :
                $componentTranslations = self::frameTranslations();
                break;
            case 'frame_effects' :
                $componentTranslations = self::frameEffectsTranslations();
                break;
            default :
                $componentTranslations = [];
                break;
        }

        if( empty( $componentTranslations ) ) return '';
        foreach( $componentTranslations as $key => $componentTranslation ) {
            if( MC2_Vars::stringContains( $term, $key ) && MC2_Vars::stringContains( $term, ' — ' ) ) return $componentTranslation;
            if( strtolower( $key ) != strtolower( $term ) ) continue;

            return $componentTranslation;
        }

        return '';
    }

    /**
     * @return array
     * This array is in an a heirarchy. DON'T CHANGE ORDER WITHOUT CHECKING FIRST!
     */
    public static function typeLineTranslations() : array {
        return [
            'Legendary Artifact Creature' => self::TYPE_LINE_CREATURE,
            'Artifact Creature'           => self::TYPE_LINE_CREATURE,
            'Artifact Land'               => self::TYPE_LINE_NON_CREATURE,
            'Tribal Artifact'             => self::TYPE_LINE_NON_CREATURE,
            'Artifact'                    => self::TYPE_LINE_NON_CREATURE,

            'Legendary Snow Land' => self::TYPE_LINE_NON_CREATURE,
            'Basic Land'          => self::TYPE_LINE_NON_CREATURE,
            'Basic Snow Land'     => self::TYPE_LINE_NON_CREATURE,
            'Land Creature'       => self::TYPE_LINE_CREATURE,
            'Land'                => self::TYPE_LINE_NON_CREATURE,

            'Enchantment Creature' => self::TYPE_LINE_CREATURE,
            'Legendary Creature'   => self::TYPE_LINE_CREATURE,
            'Creature'             => self::TYPE_LINE_CREATURE,
            'Enchantment'          => self::TYPE_LINE_NON_CREATURE,

            'Instant' => self::TYPE_LINE_NON_CREATURE,

            'Legendary Artifact'         => self::TYPE_LINE_NON_CREATURE,
            'Legendary Enchantment'      => self::TYPE_LINE_NON_CREATURE,
            'Legendary Land'             => self::TYPE_LINE_NON_CREATURE,
            'Legendary Planeswalker'     => self::TYPE_LINE_PLANESWALKER,
            'Legendary Snow Enchantment' => self::TYPE_LINE_NON_CREATURE,

            'Legendary Sorcery' => self::TYPE_LINE_NON_CREATURE,
            'Sorcery'           => self::TYPE_LINE_NON_CREATURE,
            'Summon'            => self::TYPE_LINE_CREATURE,
            'Planeswalker'      => self::TYPE_LINE_PLANESWALKER,

            'Tribal Enchantment' => self::TYPE_LINE_NON_CREATURE,
            'Tribal Instant'     => self::TYPE_LINE_NON_CREATURE,
            'Tribal Sorcery'     => self::TYPE_LINE_NON_CREATURE,
            'World Enchantment'  => self::TYPE_LINE_NON_CREATURE,
        ];
    }

    /**
     * @return string[]
     */
    public static function layoutTranslations() : array {
        return [
            'normal'    => 'Normal',
            'saga'      => 'Saga',
            'leveler'   => 'Leveler',
            'flip'      => 'Flip',
            'transform' => 'Transform',
            'meld'      => 'Meld',
            'adventure' => 'Adventure',
        ];
    }

    /**
     * @return string[]
     */
    public static function frameTranslations() : array {
        return [
            '2015'   => 'Modern (Post M15)',
            '2003'   => 'Modern (Post 8ED)',
            '1997'   => '97 Classic (Post Mirage)',
            '1993'   => '93 Classic (Alpha)',
            'future' => 'Future',
        ];
    }

    /**
     * @return string[]
     */
    public static function frameEffectsTranslations() : array {
        return [
            'originpwdfc'    => 'Flip Walker',
            'legendary'      => 'Legendary',
            'compasslanddfc' => 'Compass Flip (XLN/RIX)',
            'sunmoondfc'     => 'Sun Moon Flip (ISD/DKA/SOI)',
            'mooneldrazidfc' => 'Eldrazi Flip (EMN)',
        ];
    }

    /**
     * @param int $id
     *
     * @return mixed|string
     */
    public static function setCode( $id = 0 ) {
        $id = self::get_printing_from_id( $id );
        if( empty( $id ) ) return '';

        return get_post_meta( $id, MC2_Mtg_Printing::META_SET_CODE, true );
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public static function get_printing_from_id( $id = 0 ) : int {
        if( empty( $id ) ) $id = get_the_ID();
        $postType = get_post_type( $id );
        if( empty( $id ) ) return 0;
        switch( $postType ) {
            case 'product' :
                return MC2_Alter_Functions::printing( $id );
            case 'printing' :
                return $id;
            default :
                return 0;
        }
    }

    /**
     * @param array $frameElements
     *
     * @return string
     */
    public static function codeFromElements( $frameElements = [] ) : string {
        if( empty( $frameElements ) ) return '';
        $framecode = '';
        foreach( $frameElements as $key => $frameElement ) {
            if( empty( $frameElement ) ) continue;
            switch( $key ) {
                case 'border' :
                    $key = 'b';
                    break;
                case 'full_art' :
                    $key = 'fa';
                    break;
                case 'type_line' :
                    $key = 'tl';
                    break;
                case 'era' :
                    $key = 'fr';
                    break;
                case 'frame_effect' :
                    $key = 'fe';
                    break;
                case 'layout' :
                    $key = 'ly';
                    break;
                case 'rarity' :
                    $key = 'r';
                    break;
                case 'foil_stamp' :
                    $key = 'fs';
                    break;
                default :
                    break;
            }
            $framecode .= $key.$frameElement;
        }
        if( strpos( $framecode, 'fa0' ) !== false && strpos( $framecode, 'tl0' ) !== false && strpos( $framecode,
                                                                                                      'fr0' ) !== false && strpos( $framecode,
                                                                                                                                   'fe0' ) !== false && strpos( $framecode,
                                                                                                                                                                'ly0' ) !== false && strpos( $framecode,
                                                                                                                                                                                             'fs0' ) !== false ) {
            $framecode = 'universal';
        }

        return trim( strtolower( $framecode ) );
    }

    /**
     * @return int[]|WP_Post[]
     */
    public static function getWithoutEdhrecRank() : array {
        $params = [
            'meta_query' => [
                'key'     => 'mc_edhrec_rank',
                'compare' => 'NOT EXISTS',
            ],
        ];

        return self::query( $params );
    }

    public static function is_basic_land( $id = 0 ) : bool {
        $id = self::get_printing_from_id( $id );
        if( empty( $id ) ) return false;
        $printing  = new MC2_Mtg_Printing( $id );
        $name_card = $printing->name;
        if( in_array( $name_card, self::getBasicLandNames() ) ) return true;

        return false;
    }

    /**
     * @param bool $lower
     *
     * @return string[]
     */
    public static function getBasicLandNames( $lower = false ) : array {
        $lands = [ 'Forest', 'Island', 'Mountain', 'Swamp', 'Plains', 'Dryad Arbor' ];
        if( $lower ) foreach( $lands as $key => $land ) $lands[ $key ] = strtolower( $land );

        return $lands;
    }

    /**
     * @param int $id
     *
     * @return array|int|mixed|WP_Post
     */
    public static function idForSelection( $id = 0 ) {
        $fromParams = self::idFromParams();
        if( !empty( $fromParams ) ) return $fromParams;

        if( empty( $id ) ) return 0;
        $type = get_post_type( $id );
        switch( $type ) {
            case 'design' :
                $idAlter     = MC2_Alter_Functions::design_alter( $id );
                $printing_id = MC2_Alter_Functions::printing( $idAlter );
                break;
            case 'product' :
                $printing_id = MC2_Alter_Functions::printing( $id );
                break;
            case 'printing' :
                $printing_id = $id;
                break;
            default :
                return 0;
        }

        if( isset( $_GET['card_id'] ) ) {
            $idCard    = $_GET['card_id'];
            $framecode = wp_get_object_terms( $printing_id, 'frame_code' );
            if( !empty( $framecode ) ) {
                $printingArgs = [
                    'post_type'      => 'printing',
                    'post_status'    => [ 'publish' ],
                    'posts_per_page' => 1,
                    'tax_query'      => [
                        'RELATION' => 'AND',
                        [
                            'taxonomy' => 'mtg_card',
                            'field'    => 'term_id',
                            'terms'    => $idCard,
                        ],
                        [
                            'taxonomy' => 'frame_code',
                            'field'    => 'name',
                            'terms'    => $framecode,
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
                $printings    = get_posts( $printingArgs );
                if( !empty( $printings ) ) $printing_id = $printings[0];
            }
        }

        return $printing_id;
    }

    /**
     * @param array $args
     *
     * @return int|mixed|WP_Post
     */
    public static function idFromParams( $args = [] ) {
        if( isset( $_GET['printing_id'] ) ) return $_GET['printing_id'];
        $idCard = isset( $args['card_id'] ) ? $args['card_id'] : '';
        $idCard = isset( $_GET['card_id'] ) ? $_GET['card_id'] : $idCard;
        $idSet  = isset( $args['set_id'] ) ? $args['set_id'] : '';
        $idSet  = isset( $_GET['set_id'] ) ? $_GET['set_id'] : $idSet;
        if( empty( $idCard ) && empty( $idSet ) ) return 0;
        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => -1,
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'mtg_card',
                    'field'    => 'term_id',
                    'terms'    => $idCard,
                ],
                [
                    'taxonomy' => 'mtg_set',
                    'field'    => 'term_id',
                    'terms'    => $idSet,
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

        $printings = get_posts( $printingArgs );
        if( !empty( $printings ) && is_array( $printings ) ) return $printings[0];

        return 0;
    }

    /**
     * @return array
     */
    public static function getPostTypeSettings() : array {
        return [
            'name'                => self::NAME,
            'label'               => self::READABLE_NAME.'s',
            'label_singular'      => self::READABLE_NAME,
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 6,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'menu_icon'           => 'dashicons-format-image',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
        ];
    }

}