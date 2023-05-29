<?php

namespace Mythic_Core\Functions;

use MC_Alter_Functions;
use MC_Mtg_Printing;
use MC_Vars;
use Mythic_Core\Abstracts\MC_Post_Type_Functions;
use WP_Post;
use WP_Term;

/**
 * Class MC_Mtg_Printing_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Mtg_Printing_Functions extends MC_Post_Type_Functions {
    
    const NAME          = 'printing';
    const READABLE_NAME = 'Printing';
    
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
     * @param int $id
     *
     * @return string
     */
    public static function codeFromId( $id = 0 ) {
        if( empty( $id ) ) return 0;
        
        $frameElements = wp_get_object_terms( $id, 'mtg_frame' );
        
        if( empty( $frameElements ) && get_post_type( $id ) == 'printing' ) self::frameSync( $id );
        //if( empty( $frameElements ) || has_term( 'Universal', 'mtg_frame', $id ) ) return 'universal';
        $frameEffects = [];
        foreach( $frameElements as $key => $frameElement ) {
            $parent    = $frameElement->parent;
            $idElement = $frameElement->term_id;
            if( $idElement == 35757 ) continue;
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
                $parent    = MC_Vars::readableToKey( $parent );
                $idElement = $frameElement->term_id;
                
                if( $idElement == 24180 ) {
                    $idElement = $idElement.self::setCode( $id );
                }
                $frameElements[ $parent ] = $idElement;
            } else {
                $parent                   = MC_Vars::readableToKey( $frameElement->name );
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
            $meta = get_post_meta( $printing_id, 'mc_'.$key, true );
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
                    $year = get_post_meta( $printing_id, 'mc_frame', true );
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
            if( MC_Vars::stringContains( $term, $key ) && MC_Vars::stringContains( $term, ' — ' ) ) return $componentTranslation;
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
        
        return get_post_meta( $id, MC_Mtg_Printing::META_SET_CODE, true );
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
                return MC_Alter_Functions::printing( $id );
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
            if( $frameElement == 35757 ) continue;
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
    
    public static function is_basic_land( $id = 0 ) : bool {
        $id = self::get_printing_from_id( $id );
        if( empty( $id ) ) return false;
        $printing  = new MC_Mtg_Printing( $id );
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
                $idAlter     = MC_Alter_Functions::design_alter( $id );
                $printing_id = MC_Alter_Functions::printing( $idAlter );
                break;
            case 'product' :
                $printing_id = MC_Alter_Functions::printing( $id );
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
            'public'              => false,
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