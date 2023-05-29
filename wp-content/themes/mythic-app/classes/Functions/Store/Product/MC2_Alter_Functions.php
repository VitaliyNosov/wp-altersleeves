<?php

namespace Mythic\Functions\Store\Product;

use Exception;
use Intervention\Image\ImageManagerStatic;
use MC2_Render;
use Mythic\Abstracts\MC2_Post_Type_Functions;
use Mythic\Functions\Access\MC2_Environment;
use Mythic\Functions\MC2_Mtg_Card_Functions;
use Mythic\Functions\MC2_Mtg_Printing_Functions;
use Mythic\Functions\Store\MC2_Cart_Item_Functions;
use Mythic\Functions\Store\MC2_Order_Item_Functions;
use Mythic\Functions\User\MC2_User_Functions;
use Mythic\Functions\Wordpress\MC2_WP;
use Mythic\Helpers\MC2_Images;
use Mythic\Helpers\MC2_Meta;
use Mythic\Helpers\MC2_Vars;
use Mythic\Objects\MC2_Alter;
use Mythic\Objects\MC2_Card;
use Mythic\Objects\MC2_Design;
use Mythic\Objects\MC2_Printing;
use Mythic\Objects\MC2_Ranked_Sale;
use Mythic\Objects\MC2_Set;
use Mythic\System\MC2_Statuses;
use Mythic\Utils\MC2_Scryfall;
use WP_Error;
use WP_Term;

/**
 * Class MC2_Alter_Functions
 *
 * @package Mythic\Objects
 */
class MC2_Alter_Functions {
    
    public static $post_type = 'product';
    
    /**
     * Universal Design query - ALWAYS use this to query!
     *
     * @param array $params
     * @param false $all
     *
     * @return array
     */
    public static function query( $params = [], $all = false ) : array {
        $args = [
            'post_type'      => self::$post_type,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];
        foreach( $params as $key => $param ) $args[ $key ] = $param;
        
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => [ 'alter' ],
            'compare'  => 'IN',
        ];
        if( $all ) $args['post_status'] = MC2_Statuses::keys();
        
        return get_posts( $args );
    }
    
    /**
     * @param null $design
     *
     * @return mixed
     */
    public static function card( $design = null ) {
        if( empty( $design ) ) return 0;
        if( !is_object( $design ) ) $design = new MC2_Alter( $design );
        if( empty( $design ) ) return 0;
        $printing = self::printingObject( $design );
        if( empty( $printing ) ) return 0;
        
        return new MC2_Card( $printing->card_id ) ?? 0;
    }
    
    /**
     * @param null $design
     *
     * @return mixed
     */
    public static function printingObject( $design = null ) : ?MC2_Printing {
        $printing_id = self::printingId( $design );
        
        return !empty( $printing_id ) ? new MC2_Printing( $printing_id ) : null;
    }
    
    /**
     * @param null $design
     *
     * @return int
     */
    public static function printingId( $design = null ) : int {
        if( empty( $design ) ) return 0;
        if( !is_object( $design ) ) $design = new MC2_Alter( $design );
        if( empty( $design ) ) return 0;
        
        return $design->linkedPrinting ?? 0;
    }
    
    /**
     * @param int $design_id
     *
     * @return int|null
     */
    public static function framecodeId( $design_id = 0 ) {
        $printing = self::printingObject( $design_id );
        if( empty( $printing ) ) return null;
        
        return $printing->framecode_id;
    }
    
    /**
     * Updates the design titles to be more readable in the back end
     *
     * @param bool $force
     */
    public static function updateNames( $force = false ) {
        $designs = self::query( [
                                    'post_status' => MC2_Statuses::keys(),
                                    'meta_query'  => [
                                        [
                                            'key'     => 'mc_previous_title',
                                            'compare' => 'NOT EXISTS',
                                        ],
                                    ],
                                ] );
        foreach( $designs as $alter_id ) {
            $design        = new MC2_Alter( $alter_id );
            $current_title = get_the_title( $alter_id );
            update_post_meta( $alter_id, 'mc_previous_title', $current_title );
            
            // Get Artist Name
            $artist_id   = $design->artist_id;
            $artist      = get_user_by( 'id', $artist_id );
            $artist_name = $artist->user_login;
            
            // Default Card Name
            $printing_id = $design->linkedPrinting;
            if( empty( $printing_id ) ) continue;
            $printing  = new MC2_Printing( $printing_id );
            $card_name = $printing->name ?? '';
            
            // Crop Variation
            $crop_variations = wp_get_object_terms( $alter_id, 'alter_type' );
            $crop_variation  = !empty( $crop_variations ) ? $crop_variations[0]->name : '';
            
            $title = '['.$artist_name.'] '.$card_name.'';
            if( !empty( $crop_variation ) ) $title = $title.' ('.$crop_variation.')';
            $slug = !empty( $crop_variation ) ? $alter_id.'-'.$crop_variation : $alter_id;
            
            wp_update_post( [
                                'ID'         => $alter_id,
                                'post_title' => 'Design: '.$title,
                                'slug'       => sanitize_title( $slug ),
                            ] );
        }
    }
    
    /**
     * @param null   $alter
     * @param string $returns
     *
     * @return int|mixed|null
     */
    public static function group( $alter = null, $returns = 'id' ) {
        if( empty( $alter ) ) return null;
        $alter_id     = is_object( $alter ) ? $alter->id : $alter;
        $design_group = wp_get_object_terms( $alter_id, 'design_group' );
        if( empty( $design_group ) ) return null;
        $design_group = $design_group[0];
        if( $returns == 'id' ) return $design_group->term_id;
        
        return $design_group;
    }
    
    /**
     * Update the associated cards with a design
     *
     * @param bool $force
     */
    public static function updateCardAssociations( $force = false ) {
        if( !$force && !MC2_Environment::live() ) return;
        $designs = self::query( [ 'post_status' => MC2_Statuses::keys() ], true );
        foreach( $designs as $design_id ) {
            $card = self::card( $design_id );
            if( empty( $card ) ) continue;
            wp_set_object_terms( $design_id, $card->id, 'mtg_card', true );
        }
    }
    
    /**
     * Update the associated cards with a design
     *
     * @param bool $force
     */
    public static function updateFrameCodes( $force = false ) {
        if( !$force && !MC2_Environment::live() ) return;
        $designs = self::query( [ 'post_status' => MC2_Statuses::keys() ], true );
        foreach( $designs as $design_id ) {
            $framecode_id = self::framecodeId( $design_id );
            if( empty( $framecode_id ) ) continue;
            wp_set_object_terms( $design_id, $framecode_id, 'frame_code', false );
        }
    }
    
    /**
     * Updates the crop variation results
     *
     * @param bool $force
     */
    public static function updateDesignResults( $force = false ) {
        if( !$force && !MC2_Environment::live() ) return;
        $cards = MC2_Mtg_Card_Functions::query();
        foreach( $cards as $card ) {
            $card_id       = $card->term_id;
            $designs       = MC2_Alter_Functions::queryByCard( $card_id );
            $design_groups = [];
            $results       = [];
            foreach( $designs as $design_id ) {
                $design_group = MC2_Alter_Functions::group( $design_id );
                if( empty( $design_group ) || in_array( $design_group, $design_groups ) ) continue;
                $results[]       = $design_id;
                $design_groups[] = $design_group;
            }
            if( empty( $results ) ) continue;
            update_term_meta( $card_id, 'mc_design_results', $results );
        }
    }
    
    /**
     * @param mixed $card
     *
     * @return array
     */
    public static function queryByCard( $card = null ) : array {
        if( empty( $card ) ) return [];
        $card_id = is_object( $card ) ? $card->id : $card;
        
        return self::query( [
                                'tax_query' => [
                                    [
                                        'taxonomy' => 'mtg_card',
                                        'field'    => 'term_id',
                                        'terms'    => [ $card_id ],
                                    ],
                                ],
                            ], true );
    }
    
    /**
     * @param null $design
     *
     * @return array
     */
    public static function printingIds( $design = null ) : array {
        if( empty( $design ) ) return [ self::printingId( $design ) ];
        if( !is_object( $design ) ) $design = new MC2_Alter( $design );
        if( empty( $design ) ) return [ self::printingId( $design ) ];
        
        return [ $design->linkedPrintings ?? self::printingId( $design ) ];
    }
    
    /**
     * @param int $design_id
     *
     * @return null
     */
    public static function framecode( $design_id = 0 ) {
        $printing = self::printingObject( $design_id );
        if( empty( $printing ) ) return null;
        
        return $printing->framecode;
    }
    
    /**
     * Updates user metas for their designs
     * // @Todo Try to remember WTF this is for
     *
     * @param array $args
     */
    public static function updateArtistDesignGroups( $args = [] ) {
        return;
        $designs = self::query();
        $artists = [];
        foreach( $designs as $design_id ) {
            $design       = new MC2_Alter( $design_id );
            $design_group = MC2_Alter_Functions::group( $design_id );
            $artist_id    = $design->artist_id;
            if( !in_array( $artist_id, $artists ) ) {
                $current_groups = [];
                $artists[]      = $artist_id;
            } else {
                $current_groups = get_user_meta( $artist_id, 'mc_design_groups', true );
            }
            if( isset( $current_groups[ $design_group ] ) ) continue;
            $current_groups[ $design_group ] = $design_id;
            update_user_meta( $artist_id, 'mc_design_groups', $current_groups );
            break;
        }
    }
    
    /**
     * @param array $args
     */
    public static function render( $args = [] ) {
        MC2_Render::item( 'alter', '', $args );
    }
    
    /**
     * @param int $limit
     *
     * @return array
     */
    public static function bestsellingResults() : array {
        $results = MC2_Ranked_Sale::getForHome();
        $count   = 0;
        foreach( $results as $key => $result ) {
            $product_id = $result->product_id;
            if( get_post_status( $product_id ) != 'publish' ) {
                unset( $results[ $key ] );
                continue;
            }
            $count++;
            $results[ $key ] = $result->product_id;
        }
        
        return $results;
    }
    
    /**
     * @param int $limit
     *
     * @return array
     */
    public static function recentResults( $limit = 0 ) : array {
        $results = get_option( 'mc_recent_alters', [] );
        if( empty( $limit ) ) $limit = is_front_page() ? 12 : count( $results );
        
        return array_slice( $results, 0, $limit );
    }
    
    public static function updateRecentResults() {
        delete_option( 'mc_recent_alters' );
        $args            = [
            'post_type'      => 'product',
            'posts_per_page' => 300,
            'post_status'    => 'publish',
            'orderby'        => 'ID',
            'order'          => 'DESC',
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => 'alter',
                ],
            ],
            'fields'         => 'ids',
        ];
        $alters          = get_posts( $args );
        $design_groups   = [];
        $results         = [];
        $count           = 1;
        $artists_on_page = [];
        foreach( $alters as $alter_id ) {
            $design_group_id = self::designGroupId( $alter_id );
            if( empty( $design_group_id ) || in_array( $design_group_id, $design_groups ) ) continue;
            $alter     = new MC2_Alter( $alter_id );
            $artist_id = $alter->author_id;
            if( in_array( $artist_id, $artists_on_page ) ) continue;
            $results[]         = $alter_id;
            $artists_on_page[] = $artist_id;
            if( count( $artists_on_page ) >= 12 ) $artists_on_page = [];
            update_option( 'mc_recent_alters', $results );
            $count++;
            if( $count == 100 ) break;
        }
    }
    
    /**
     * @param null $alter
     *
     * @return int
     */
    public static function designGroupId( $alter = null ) {
        if( empty( $alter ) ) return 0;
        $design_group = self::designGroup( $alter );
        if( empty( $design_group ) ) return 0;
        
        return $design_group->term_id;
    }
    
    /**
     * @param null $alter
     *
     * @return mixed|WP_Term|null
     */
    public static function designGroup( $alter = null ) {
        if( empty( $alter ) ) return null;
        $alter_id      = is_object( $alter ) ? $alter->id : $alter;
        $design_groups = wp_get_object_terms( $alter_id, 'design_group' );
        if( empty( $design_groups ) || !is_array( $design_groups ) ) return null;
        
        return $design_groups[0];
    }
    
    /**
     * @param int $idAlter
     *
     * @return bool|false|string
     */
    public static function progress( $idAlter = 0 ) {
        $status = get_post_status( $idAlter );
        switch( $status ) {
            case 'verify' :
                $newStatus = 'publish';
                wp_delete_object_term_relationships( $idAlter, 'alter_status' );
                break;
            case 'internal_verify' :
                $newStatus = 'internal_approved';
                wp_delete_object_term_relationships( $idAlter, 'alter_status' );
                break;
            case 'internal_action' :
                $newStatus = 'internal_verify';
                break;
            case 'pending' :
            case 'action' :
                $newStatus = 'verify';
                break;
            default:
                $newStatus = $status;
                break;
        }
        wp_update_post( [
                            'ID'          => $idAlter,
                            'post_status' => $newStatus,
                        ] );
        $idDesign = MC2_Alter_Functions::design( $idAlter );
        if( !empty( $idDesign ) && $newStatus == 'publish' ) {
            wp_update_post( [
                                'ID'          => $idDesign,
                                'post_status' => 'publish',
                            ] );
        }
        
        /*\
        // @Todo add back in log
         *     $log = new Log();
         *
         *

        $idAdmin   = wp_get_current_user()->ID;
        $dataAdmin = get_userdata( $idAdmin );
        $nameAdmin = $dataAdmin->first_name;
         *   $message = "Alter '$idAlter' had its status changed from '$status' to '$newStatus' by $nameAdmin ($idAdmin)";
        $log->setIdAdmin( $idAdmin );
        $log->setIdUser( MC2_WP::authorId( $idAlter ) );
        $log->setIdItem( $idAlter );
        $log->setCategory( 101 );
        $log->setMessage( $message );
        $log->create();
         */
        
        return $newStatus;
    }
    
    /**
     * @param int $id
     *
     * @return int
     */
    public static function design( $id = 0 ) {
        if( empty( $id ) ) return 0;
        
        return MC2_WP::meta( MC2_Alter::META_LINKED_DESIGN, $id );
    }
    
    /**
     * @return array[]
     */
    public static function design_setup_options() : array {
        return [
            'new'  => [
                'label'    => __( 'A new design', MC2_TEXT_DOMAIN ),
                'value'    => 'new',
                'selected' => true,
            ],
            'add'  => [
                'label'    => __( 'A cropping or printing variation for an existing design', MC2_TEXT_DOMAIN ),
                'value'    => 'add',
                'selected' => false,
            ],
            'link' => [
                'label'    => __( 'An artistic/aesthetic variation for an existing design ', MC2_TEXT_DOMAIN ),
                'value'    => 'link',
                'selected' => false,
                'disabled' => true,
            ],
        ];
    }
    
    /**
     * @param int $idDesign
     *
     * @return array
     */
    public static function design_alters( $idDesign = 0 ) {
        $idDesign = self::design_validate( $idDesign );
        if( empty( $idDesign ) ) return [];
        $alters = MC2_WP::meta( 'mc_linked_variations', $idDesign );
        if( empty( $alters ) || !is_array( $alters ) ) return [];
        
        return $alters;
    }
    
    /**
     * @param int $id
     *
     * @return false|int
     */
    public static function design_validate( $id = 0 ) {
        if( empty( $id ) ) $id = MC2_WP::currentId();
        if( get_post_type( $id ) == 'design' ) return $id;
        if( get_post_type( $id ) == 'product' ) {
            $idProduct = self::design( $id );
            if( !empty( $idProduct ) ) return $idProduct;
        }
        
        return 0;
    }
    
    /**
     * @return int
     */
    public static function _get() {
        return $_GET['alter_id'] ?? 0;
    }
    
    /**
     * @return int
     */
    public static function design_get() {
        $idAlter  = isset( $_GET['alter_id'] ) ? $_GET['alter_id'] : '';
        $idDesign = isset( $_GET['design_id'] ) ? $_GET['design_id'] : '';
        if( empty( $idAlter ) && empty( $idDesign ) ) return self::design_idFromQueriedObject();
        
        $idDesign = self::design( $idAlter );
        if( !empty( $idDesign ) ) return $idDesign;
        
        return 0;
    }
    
    public static function design_idFromQueriedObject() {
        $object = get_queried_object();
        if( empty( $object ) || is_object( $object ) ) return 0;
        $idObject = $object->ID;
        if( get_post_type( $idObject ) == 'design' ) return $idObject;
        if( !get_post_type( $idObject ) == 'product' ) return 0;
        $idDesign = self::design( $idObject );
        if( empty( $idDesign ) ) return 0;
        
        return $idDesign;
    }
    
    /**
     * @param int    $idAlter
     * @param string $result
     *
     * @return array|string
     */
    public static function type( $idAlter = 0, $result = 'name' ) {
        $output = '';
        if( empty( $idAlter ) ) return $output;
        $types = wp_get_object_terms( $idAlter, 'alter_type' );
        if( empty( $types ) || !is_array( $types ) ) return $output;
        foreach( $types as $objectType ) {
            $idType   = $objectType->term_id;
            $nameType = $objectType->name;
            $slugType = $objectType->slug;
            break;
        }
        $result = strtolower( trim( $result ) );
        switch( $result ) {
            case 'id' :
            case 'term_id' :
                return $idType;
            case 'name' :
                return $nameType;
            case 'slug' :
                return $slugType;
            case 'all' :
                return [
                    'id'   => $idType,
                    'name' => $nameType,
                    'slug' => $slugType,
                ];
            case 'object' :
                return $objectType;
            default :
                return $output;
        }
    }
    
    /**
     * @param int $idDesign
     *
     * @return bool|mixed|string
     */
    public static function design_connected( $idDesign = 0 ) {
        if( empty( $idDesign ) ) $idDesign = self::design_get();
        $connected = MC2_WP::meta( 'mc_connected_designs', $idDesign );
        if( empty( $connected ) ) return [];
        
        return $connected;
    }
    
    /**
     * @param int $idDesign
     *
     * @return int
     */
    public static function design_alter( $idDesign = 0 ) {
        $idDesign = self::design_validate( $idDesign );
        if( empty( $idDesign ) ) return 0;
        $variations = self::design_alters( $idDesign );
        if( empty( $variations ) ) return 0;
        if( is_array( $variations ) ) return $variations[0];
        if( is_numeric( $variations ) ) return $variations;
        
        return 0;
    }
    
    /**
     * @param int $idDesign
     * @param int $target
     *
     * @return false|string
     */
    public static function design_displaySlider( int $idDesign = 0, int $target = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/store/product/components/slider.php' );
        
        return ob_get_clean();
    }
    
    /**
     * @param int $idDesign
     * @param int $target
     *
     * @return false|string
     */
    public static function design_displayInfo( int $idDesign = 0, int $target = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/store/product/components/info.php' );
        
        return ob_get_clean();
    }
    
    /**
     * @param int $idAlter
     * @param int $printing_id
     *
     * @return string
     */
    public static function getCombinedImage( $idAlter = 0, $printing_id = 0 ) {
        if( empty( $idAlter ) ) return '';
        return MC2_Alter_Functions::metaFileCombinedLoResJpg( $idAlter, $printing_id );
    }
    
    /**
     * @param int $idAlter
     * @param int $printing_id
     *
     * @return string
     */
    public static function metaFileCombinedLoResJpg( $idAlter = 0, $printing_id = 0 ) {
        if( empty( $idAlter ) ) return '';
        if( !empty( $printing_id ) ) return MC2_Alter_Functions::urlImageBrowsing( $idAlter, $printing_id );
        $meta = trim( MC2_WP::meta( MC2_Alter::META_FILE_COMBINED_LO_JPG, $idAlter ) );
        if( empty( $meta ) ) return SITE_URL_IMG.'/unavailable-card.png';
        
        return $meta;
    }
    
    /**
     * @param int $idAlter
     * @param int $printing_id
     *
     * @return string
     */
    public static function urlImageBrowsing( $idAlter = 0, $printing_id = 0 ) {
        if( empty( $idAlter ) || empty( $printing_id ) ) return '';
        $fallback        = trim( MC2_WP::meta( MC2_Alter::META_FILE_COMBINED_LO_JPG, $idAlter ) );
        $idCreator       = MC2_WP::authorId( $idAlter );
        $creatorUsername = get_the_author_meta( 'user_nicename', $idCreator );
        $path            = '/files/alterists/'.$creatorUsername.'/designs/'.$idAlter.'/browsing/'.$printing_id.'.jpg';
        $dirPath         = ABSPATH.$path;
        if( !file_exists( $dirPath ) ) return $fallback;
        
        return get_site_url().$path;
    }
    
    /**
     * @param int $id
     */
    public static function design_frameSync( $id = 0 ) {
        $type = get_post_type( $id );
        if( $type != 'product' && $type != 'design' ) return;
        if( $type == 'product' ) {
            $id = self::design( $id );
            if( empty( $id ) ) return;
        }
        $alters = self::design_alters( $id );
        if( empty( $alters ) ) return;
        wp_delete_object_term_relationships( $id, 'frame_code' );
        foreach( $alters as $alter ) MC2_Alter_Functions::frameSync( $alter );
    }
    
    /**
     * @param int $idAlter
     */
    public static function frameSync( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return;
        wp_delete_object_term_relationships( $idAlter, 'frame_code' );
        $printing_id         = self::printing( $idAlter );
        $printingsAdditional = self::metaAdditionalPrintings();
        if( empty( $printing_id ) && empty( $printingsAdditional ) ) return;
        $printings   = is_array( $printingsAdditional ) ? $printingsAdditional : [];
        $printings[] = $printing_id;
        $printings   = array_unique( $printings );
        $keys        = [ 'type_line', 'layout', 'frame', 'frame_effects', 'rarity', 'full_art' ];
        foreach( $keys as $key ) {
            $meta = get_printing_meta( $printing_id, MC2_HOOK.$key, true );
            if( $key != 'frame_effects' ) {
                $meta = MC2_Mtg_Printing_Functions::componentTranslator( $meta, $key );
                $tax  = get_term_by( 'name', $meta, 'mtg_frame' );
                if( is_object( $tax ) ) {
                    $tax = $tax->term_id;
                    wp_set_post_terms( $idAlter, [ $tax ], 'mtg_frame', true );
                }
                continue;
            } else {
                if( $key == 'rarity' && !empty( $meta ) ) {
                    $tax  = get_term_by( 'name', 'Foil Stamp', 'mtg_frame' );
                    $year = get_printing_meta( $printing_id, 'mc_frame', true );
                    $year = trim( $year );
                    if( $year != 2015 || ( $meta != 'mythic' && $meta != 'rare' ) ) {
                        break;
                    }
                    if( is_object( $tax ) ) {
                        $tax = $tax->term_id;
                        wp_set_post_terms( $idAlter, [ $tax ], 'mtg_frame', true );
                    }
                } else {
                    if( $key == 'full_art' && !empty( $meta ) ) {
                        $tax = get_term_by( 'name', 'Full Art', 'mtg_frame' );
                        if( is_object( $tax ) ) {
                            $tax = $tax->term_id;
                            wp_set_post_terms( $idAlter, [ $tax ], 'mtg_frame', true );
                        }
                    }
                }
            }
            $frameEffects = is_array( $meta ) ? $meta : [ $meta ];
            foreach( $frameEffects as $effect ) {
                $tax = get_term_by( 'name', $effect, 'mtg_frame' );
                if( is_object( $tax ) ) {
                    $tax = $tax->term_id;
                    wp_set_post_terms( $idAlter, [ $tax ], 'mtg_frame', true );
                }
            }
        }
        $idDesign = self::design( $idAlter );
        
        foreach( $printings as $printing_id ) {
            wp_delete_object_term_relationships( $idAlter, 'frame_code' );
            $framecode = MC2_Mtg_Printing_Functions::getFrameCodeId( $printing_id );
            if( empty( $framecode ) ) {
                MC2_Mtg_Printing_Functions::frameSync( $printing_id );
                $framecode = MC2_Mtg_Printing_Functions::getFrameCodeId( $printing_id );
            }
            $objectFramecode = get_term_by( 'name', $framecode, 'frame_code' );
            if( empty( $objectFramecode ) ) {
                $objectFramecode = wp_insert_term( $framecode, 'frame_code' );
            }
            $idFrameCode = $objectFramecode->term_id;
            wp_set_object_terms( $idAlter, [ (int) $idFrameCode ], 'frame_code', false );
            update_post_meta( $idAlter, 'mc_frame_synced', time() );
            
            if( !empty( $idDesign ) ) {
                wp_set_object_terms( $idDesign, [ MC2_Mtg_Printing_Functions::getFrameCodeId( $printing_id ) ], 'frame_code',
                                     true );
            }
            
            update_post_meta( $idAlter, 'mc_frame_code', $framecode );
            break;
        }
        
        update_post_meta( $idAlter, 'mc_frame_synced', time() );
    }
    
    /**
     * @param null $design
     *
     * @return int
     */
    public static function printing( $design = null ) : int {
        return self::printingId( $design );
    }
    
    /**
     * @param int $idAlter
     *
     * @return int
     */
    public static function metaAdditionalPrintings( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return 0;
        
        return MC2_WP::meta( MC2_Alter::META_ADDITIONAL_PRINTINGS, $idAlter );
    }
    
    /**
     * @param int $idDesign
     *
     * @return bool
     */
    public static function design_potentiallyGeneric( $idDesign = 0 ) : bool {
        if( empty( $idDesign ) ) return false;
        foreach( self::design_genericTypes() as $generic_type ) {
            if( has_term( $generic_type, 'alter_type', $idDesign ) ) return true;
        }
        
        return false;
    }
    
    /**
     * @return int[]
     */
    public static function design_genericTypes() : array {
        return [ 26161, 26158, 26159, 35867, 35868 ];
    }
    
    /**
     * @param int $design_id
     *
     * @return array
     */
    public static function get_alter_types( $design_id = 0 ) : array {
        $design_id     = !empty( MC2_Alter_Functions::design( $design_id ) ) ? MC2_Alter_Functions::design( $design_id ) : $design_id;
        $d_alter_types = wp_get_object_terms( $design_id, 'alter_type' );
        $d_selected    = !empty( $d_alter_types ) && is_array( $d_alter_types ) ? $d_alter_types[0]->term_id : 0;
        $types         = [];
        $alter_types   = get_terms( [ 'taxonomy' => 'alter_type', 'hide_empty' => false ] );
        
        foreach( $alter_types as $key => $alter_type ) {
            $alter_type_id            = $alter_type->term_id;
            $alter_type_name          = $alter_type->name;
            $alter_type_name_parsable = strtolower( $alter_type_name );
            $generic_available        = MC2_Vars::stringContains( $alter_type_name_parsable, 'art replac' ) ? 1 : 0;
            $generic_selected         = MC2_Vars::stringContains( $alter_type_name_parsable,
                                                                 'frame' ) || MC2_Vars::stringContains( $alter_type_name_parsable,
                                                                                                       'generic' ) ? 1 : 0;
            $transferable             = $generic_selected || $generic_available ? 1 : 0;
            $selected                 = $d_selected == $alter_type_id ? 1 : 0;
            $types[]                  = [
                'id'               => $alter_type_id,
                'name'             => $alter_type_name,
                'generic'          => $generic_available,
                'generic_selected' => $generic_selected,
                'transferable'     => $transferable,
                'selected'         => $selected,
            ];
        }
        
        return $types;
    }
    
    /**
     * @return int|WP_Error
     */
    public static function create_empty_alter() {
        if( !is_user_logged_in() ) return 0;
        $temp_name  = MC2_Vars::generate( 10 );
        $creator_id = wp_get_current_user()->ID;
        $alter_id   = wp_insert_post( [
                                          'comment_status' => 'closed',
                                          'ping_status'    => 'closed',
                                          'post_author'    => $creator_id,
                                          'post_title'     => $temp_name,
                                          'post_name'      => $temp_name,
                                          'post_status'    => 'pending',
                                          'post_type'      => 'product',
                                          'post_date_gmt'  => gmdate( 'Y-m-d G:i:s' ),
                                      ] );
        if( empty( $alter_id ) ) return 0;
        $sku = "alt-$creator_id-$alter_id";
        wp_set_object_terms( $alter_id, 'simple', 'product_type' );
        update_post_meta( $alter_id, '_visibility', 'visible' );
        update_post_meta( $alter_id, '_stock_status', 'instock' );
        update_post_meta( $alter_id, 'total_sales', '0' );
        update_post_meta( $alter_id, '_regular_price', "6" );
        update_post_meta( $alter_id, '_sale_price', "6" );
        update_post_meta( $alter_id, '_weight', "3" );
        update_post_meta( $alter_id, '_sku', $sku );
        update_post_meta( $alter_id, '_product_attributes', [] );
        update_post_meta( $alter_id, '_sale_price_dates_from', "" );
        update_post_meta( $alter_id, '_sale_price_dates_to', "" );
        update_post_meta( $alter_id, '_price', "6" );
        update_post_meta( $alter_id, '_credits_amount', 1 );
        
        return $alter_id;
    }
    
    /**
     * @param int $idAlter
     *
     * @return array
     */
    public static function getFrameElements( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return [];
        $frameElements = get_the_terms( $idAlter, 'mtg_frame' );
        if( !empty( $frameElements ) || $frameElements !== null ) return $frameElements;
        
        return [];
    }
    
    /**
     * @param int $idAlter
     *
     * @return array|mixed
     */
    public static function printingsWithAlters( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return [];
        if( MC2_Alter_Functions::is_basic_land( $idAlter ) ) return [];
        
        $idDesign = self::design( $idAlter );
        if( empty( $idDesign ) ) return [];
        $typeDesign = self::design_type( $idDesign, 'key' );
        
        $additionalPrintings      = [];
        $additionalPrintingsCheck = [];
        switch( $typeDesign ) {
            case 'art_replacement' :
                $designVariations = self::design_alters( $idDesign );
                foreach( $designVariations as $key => $designVariation ) {
                    $status = get_post_status( $designVariation );
                    if( MC2_User_Functions::authorCurrentObject( $designVariation ) && $status != 'publish' && !MC2_User_Functions::isAdmin() ) continue;
                    $printing_id = $_GET['printing_id'] ?? MC2_Alter_Functions::printing( $designVariation );
                    
                    $framecode = MC2_Mtg_Printing_Functions::getFrameCodeId( $printing_id );
                    if( empty( $framecode ) ) continue;
                    $idCard = MC2_Mtg_Printing_Functions::getCardId( $printing_id );
                    if( empty( $idCard ) ) continue;
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
                                'taxonomy' => 'frame_code',
                                'field'    => 'name',
                                'terms'    => $framecode,
                            ],
                        ],
                        'fields'         => 'ids',
                    ];
                    $printings    = get_posts( $printingArgs );
                    $printings[]  = self::printing( $idAlter );
                    $printings    = array_unique( $printings );
                    foreach( $printings as $printing ) {
                        if( in_array( $printing, $additionalPrintingsCheck ) ) continue;
                        $sets = wp_get_object_terms( $printing, 'mtg_set' );
                        if( empty( $sets ) || !is_array( $sets ) ) continue;
                        foreach( $sets as $set ) {
                            if( $set->parent != MC2_Set::availableId() ) continue;
                            $additionalPrintings[] = [
                                'alter_id' => $designVariation,
                                'value'    => $printing,
                                'text'     => $set->name,
                            ];
                        }
                        $additionalPrintingsCheck[] = $printing;
                    }
                }
                
                $additionalPrintings = array_unique( $additionalPrintings, SORT_REGULAR );
                break;
            case 'card' :
                $designVariations = self::design_alters( $idAlter );
                if( empty( $designVariations ) ) $designVariations = [ $idAlter ];
                
                foreach( $designVariations as $key => $designVariation ) {
                    if( !is_numeric( $designVariation ) || is_array( $designVariation ) ) {
                        unset( $designVariations[ $key ] );
                    }
                    
                    $variationPrintings   = self::metaAdditionalPrintings( $designVariation );
                    $linkedPrinting       = self::printing( $designVariation );
                    $variationPrintings   = !empty( $variationPrintings ) && is_array( $variationPrintings ) ? $variationPrintings : [];
                    $variationPrintings[] = $linkedPrinting;
                    $variationPrintings   = array_unique( $variationPrintings );
                    
                    foreach( $variationPrintings as $variationPrinting ) {
                        $printing              = new MC2_Printing( $variationPrinting );
                        $additionalPrintings[] = [
                            'alter_id' => $designVariation,
                            'value'    => $variationPrinting,
                            'text'     => $printing->set_name,
                        ];
                    }
                }
                break;
        }
        
        if( !is_array( $additionalPrintings ) || empty( $additionalPrintings ) ) return [];
        if( count( $additionalPrintings ) == 1 ) return [];
        
        return $additionalPrintings;
    }
    
    /**
     * @param int $idAlter
     *
     * @return bool
     */
    public static function is_basic_land( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return false;
        
        return MC2_Mtg_Printing_Functions::is_basic_land( MC2_Alter_Functions::printing( $idAlter ) );
    }
    
    /**
     * @param int    $idDesign
     * @param string $result
     *
     * @return array|string
     */
    public static function design_type( $idDesign = 0, $result = 'name' ) {
        $output = '';
        
        if( empty( $idDesign ) ) $idDesign = self::design_get();
        if( empty( $idDesign ) ) return '';
        $types = wp_get_object_terms( $idDesign, 'design_type' );
        if( empty( $types ) || !is_array( $types ) ) return $output;
        foreach( $types as $objectType ) {
            $idType   = $objectType->term_id;
            $nameType = $objectType->name;
            $slugType = $objectType->slug;
            break;
        }
        $result = strtolower( trim( $result ) );
        switch( $result ) {
            case 'id' :
            case 'term_id' :
                return $idType;
            case 'name' :
                return $nameType;
            case 'slug' :
                return $slugType;
            case 'key' :
                return str_replace( '-', '_', $slugType );
            case 'all' :
                return [
                    'id'   => $idType,
                    'name' => $nameType,
                    'slug' => $slugType,
                ];
            case 'object' :
                return $objectType;
            default :
                return $output;
        }
    }
    
    /**
     * @param int    $idAlter
     * @param string $type
     *
     * @return string
     */
    public static function image( $idAlter = 0, $type = 'lo' ) {
        if( empty( $idAlter ) ) return 0;
        switch( $type ) {
            case 'lo' :
                return MC2_Alter_Functions::metaFileAlterLoResPng( $idAlter );
            case 'hi' :
                return MC2_Alter_Functions::metaFileAlterHiResPng( $idAlter );
            default :
                return '';
        }
    }
    
    /**
     * @param int $idAlter
     *
     * @return string
     */
    public static function metaFileAlterLoResPng( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return '';
        
        return trim( MC2_WP::meta( MC2_Alter::META_FILE_ALTER_LO_PNG, $idAlter ) );
    }
    
    /**
     * @param int $idAlter
     *
     * @return string
     */
    public static function metaFileAlterHiResPng( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return '';
        $image = MC2_WP::meta( MC2_Alter::META_FILE_ALTER_HI_PNG, $idAlter );
        if( empty( $image ) ) $image = self::metaFileAlterLoResPng( $idAlter );
        
        return trim( $image );
    }
    
    /**
     * @param int $idAlter
     *
     * @return string
     */
    public static function metaFileCombinedHiResJpg( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return '';
        
        return trim( MC2_WP::meta( MC2_Alter::META_FILE_COMBINED_HI_JPG, $idAlter ) );
    }
    
    /**
     * @param int $idAlter
     *
     * @return string
     */
    public static function metaFileCombinedHiResPng( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return '';
        
        return trim( MC2_WP::meta( MC2_Alter::META_FILE_COMBINED_LO_PNG, $idAlter ) );
    }
    
    /**
     * @param $idProduct
     *
     * @return string
     */
    public static function getAlteristProfileUrl( $idProduct ) {
        if( !get_post_status( $idProduct ) ) return '';
        $niceName = self::getAlteristNicename( $idProduct );
        
        return '/alterist/'.$niceName;
    }
    
    /**
     * @param $idProduct
     *
     * @return string
     */
    public static function getAlteristNicename( $idProduct ) {
        if( !get_post_status( $idProduct ) ) return '';
        $idCreator = MC2_WP::authorId( $idProduct );
        
        return get_the_author_meta( 'user_nicename', $idCreator );
    }
    
    /**
     * @param int $idAlter
     *
     * @return string
     */
    public static function getCartName( $idAlter = 0 ) : string {
        if( !empty( $name ) ) return $name;
        $idCreator    = MC2_WP::authorId( $idAlter );
        $nameAlterist = get_the_author_meta( 'display_name', $idCreator );
        $printing_id  = self::printing( $idAlter );
        $printing     = new MC2_Printing( $printing_id );
        
        return $printing->name.' ('.$printing->set_name.') - '.$nameAlterist.'<br><small>'.$idAlter.'</small>';
    }
    
    /**
     * @param int $alter_id
     *
     * @return string
     */
    public static function getCartImage( $alter_id = 0 ) {
        return self::getCombinedImage( $alter_id );
    }
    
    /**
     * @param array $args
     *
     * @return bool
     */
    public static function manage( $args = [] ) : bool {
        $idCreator = !empty( $args['creator'] ) ? $args['creator'] : 0;
        $idAlter   = !empty( $args['alter_id'] ) ? $args['alter_id'] : 0;
        $printings = !empty( $args['printings'] ) ? $args['printings'] : MC2_Alter_Functions::metaAdditionalPrintings( $idAlter );
        if( !empty( $idAlter ) ) $idCreator = MC2_WP::authorId( $idAlter );
        if( empty( $idCreator ) ) $idCreator = wp_get_current_user()->ID;
        $idDesign      = !empty( $args['design_id'] ) ? $args['design_id'] : 0;
        $idDesign      = !empty( $idDesign ) ? $idDesign : self::design_altersCheck( $idDesign );
        $nameDesign    = !empty( $args['design_name'] ) ? $args['design_name'] : MC2_Vars::generate( 10 );
        $frame         = !empty( $args['frame'] ) ? 1 : 0;
        $typeAlter     = !empty( $args['alter_type'] ) ? $args['alter_type'] : 0;
        $bounty        = trim( strtolower( $args['bounty'] ) );
        $product_type  = !empty( $args['product_type'] ) ? $args['product_type'] : 'alter';
        $commission_id = !empty( $args['commission_id'] ) ? $args['commission_id'] : 0;
        $generic       = !empty( $args['generic'] ) ? 1 : 0;
        $availability  = $args['availability'] ?? 'available';
        
        if( empty( $idDesign ) ) {
            $idDesign = MC2_Alter_Functions::design( $idAlter );
            if( empty( $idDesign ) ) {
                $argsDesign = [
                    'name'      => $nameDesign,
                    'artist_id' => $idCreator,
                ];
                $idDesign   = self::design_create( $argsDesign );
            }
        } else {
            if( empty( $typeAlter ) ) {
                $typesAlter = wp_get_object_terms( $idDesign, 'alter_type', [ 'fields' => 'ids' ] );
                if( !empty( $typesAlter ) && is_array( $typesAlter ) ) {
                    $typeAlter = $typesAlter[0];
                }
            }
        }
        wp_update_post( [
                            'ID'         => $idDesign,
                            'post_title' => $nameDesign,
                        ] );
        
        $tags = $args['product_tags'] ?? [];
        if( !empty( $tags ) ) $tags = json_decode( $tags );
        if( !empty( $tags ) && is_array( $tags ) ) {
            $tags = !is_array( $tags ) ? json_decode( $tags ) : $tags;
            wp_set_post_terms( $idDesign, $tags, 'product_tag', false );
        }
        
        $edit   = true;
        $alters = self::design_alters( $idDesign );
        if( empty( $idAlter ) ) {
            $edit            = false;
            $count           = is_array( $alters ) ? count( $alters ) + 1 : 1;
            $title           = $nameDesign.' - '.$idDesign.' ('.$count.')';
            $status          = $product_type == 'commission' ? 'internal_verify' : 'verify';
            $idAlter         = wp_insert_post( [
                                                   'comment_status' => 'closed',
                                                   'ping_status'    => 'closed',
                                                   'post_author'    => $idCreator,
                                                   'post_title'     => $title,
                                                   'post_name'      => MC2_Vars::generate( 15 ),
                                                   'post_status'    => $status,
                                                   'post_type'      => 'product',
                                                   'post_date_gmt'  => gmdate( 'Y-m-d G:i:s' ),
                                               ] );
            $creatorUsername = get_the_author_meta( 'user_nicename', $idCreator );
            $sku             = $creatorUsername.'-alt-'.$idAlter;
            wp_set_object_terms( $idAlter, 'simple', 'product_type' );
            update_post_meta( $idAlter, '_visibility', 'visible' );
            update_post_meta( $idAlter, '_stock_status', 'instock' );
            update_post_meta( $idAlter, 'total_sales', '0' );
            update_post_meta( $idAlter, '_regular_price', "6" );
            update_post_meta( $idAlter, '_sale_price', "6" );
            update_post_meta( $idAlter, '_weight', "3" );
            update_post_meta( $idAlter, '_sku', $sku );
            update_post_meta( $idAlter, '_product_attributes', [] );
            update_post_meta( $idAlter, '_sale_price_dates_from', "" );
            update_post_meta( $idAlter, '_sale_price_dates_to', "" );
            update_post_meta( $idAlter, '_price', "6" );
            update_post_meta( $idAlter, '_credits_amount', 1 );
        } else {
            if( get_post_status( $idAlter ) == 'action' ) {
                wp_update_post( [
                                    'ID'          => $idAlter,
                                    'post_status' => 'verify',
                                ] );
            } else {
                if( get_post_status( $idAlter ) == 'internal_action' ) {
                    wp_update_post( [
                                        'ID'          => $idAlter,
                                        'post_status' => 'internal_verify',
                                    ] );
                }
            }
        }
        
        wp_set_object_terms( $idAlter, $product_type, 'product_cat' );
        
        $bountyAlter  = get_post_meta( $idAlter, 'mc_bounty', true );
        $bountyDesign = get_post_meta( $idDesign, 'mc_bounty', true );
        $bounty       = self::sanitizeBounty( $bounty );
        if( ( empty( $bountyAlter ) && empty( $bountyDesign ) ) && !empty( $bounty ) ) {
            update_post_meta( $idAlter, 'mc_bounty', sanitize_title( $bounty ) );
            update_post_meta( $idDesign, 'mc_bounty', sanitize_title( $bounty ) );
        }
        
        if( $bounty == 'internal' || $availability == 'internal' ) {
            wp_update_post( [
                                'ID'          => $idAlter,
                                'post_status' => 'internal_verify',
                            ] );
        }
        
        // Lets link them up
        $alters[] = $idAlter;
        $alters   = array_unique( $alters );
        update_post_meta( $idDesign, 'mc_linked_variations', $alters );
        update_post_meta( $idAlter, 'mc_linked_design', $idDesign );
        
        // Lets make sure we have printings
        $printing_id     = $printings[0];
        $printingChanged = false;
        $printingPrimary = self::printing( $idAlter );
        if( $printingPrimary != $printing_id ) $printingChanged = true;
        update_post_meta( $idAlter, 'mc_linked_printing', $printing_id );
        update_post_meta( $idAlter, 'mc_additional_printings', $printings );
        
        // Add Frame Elements to the Alter
        wp_delete_object_term_relationships( $printing_id, 'mtg_frame' );
        $keys = [ 'type_line', 'layout', 'frame', 'frame_effects', 'era', 'full_art' ];
        foreach( $keys as $key ) {
            $meta = get_printing_meta( $printing_id, MC2_HOOK.$key, true );
            if( $key != 'frame_effects' ) {
                $meta = MC2_Mtg_Printing_Functions::componentTranslator( $meta, $key );
                $tax  = get_term_by( 'name', $meta, 'mtg_frame' );
                if( is_object( $tax ) ) {
                    $tax = $tax->term_id;
                    wp_set_object_terms( $idAlter, [ $tax ], 'mtg_frame', true );
                }
                continue;
            }
            $frameEffects = is_array( $meta ) ? $meta : [ $meta ];
            foreach( $frameEffects as $effect ) {
                $tax = get_term_by( 'name', $effect, 'mtg_frame' );
                if( is_object( $tax ) ) {
                    $tax = $tax->term_id;
                    wp_set_object_terms( $idAlter, [ $tax ], 'mtg_frame', true );
                }
            }
        }
        
        // And Frame Codes to Both
        $framecode = MC2_Mtg_Printing_Functions::getFrameCodeId( $printing_id );
        update_post_meta( $idAlter, 'mc_frame_code', true );
        wp_set_post_terms( $idAlter, [ $framecode ], 'frame_code', false );
        wp_set_post_terms( $idDesign, [ $framecode ], 'frame_code' );
        
        // Add Card to Both
        if( empty( $frame ) ) {
            $idCard = MC2_Mtg_Printing_Functions::getCardId( $printings[0] );
            wp_set_object_terms( $idAlter, [ $idCard ], 'mtg_card', false );
            wp_set_object_terms( $idDesign, [ $idCard ], 'mtg_card', false );
        }
        
        // Add Set to Alter
        if( empty( $frame ) ) {
            foreach( $printings as $printing_id ) {
                $printing  = new MC2_Printing( $printing_id );
                $setName   = $printing->set_name;
                $objectSet = get_term_by( 'name', $setName, 'mtg_set' );
                if( empty( $objectSet ) ) continue;
                $idSet = $objectSet->term_id;
                wp_set_object_terms( $idAlter, [ $idSet ], 'mtg_set' );
                wp_set_object_terms( $idDesign, [ $idSet ], 'mtg_set' );
            }
        }
        
        // Crop type || Design Type
        wp_delete_object_term_relationships( $idDesign, 'design_type' );
        wp_delete_object_term_relationships( $idDesign, 'alter_type' );
        foreach( $alters as $alter ) {
            $alter = (int) $alter;
            wp_delete_object_term_relationships( $alter, 'alter_type' );
            wp_delete_object_term_relationships( $alter, 'design_type' );
        }
        if( $frame ) {
            wp_set_object_terms( (int) $idDesign, 26147, 'design_type', false );
            wp_set_object_terms( (int) $idDesign, 26164, 'alter_type', false );
            foreach( $alters as $alter ) {
                $alter = (int) $alter;
                wp_set_object_terms( (int) $alter, 26164, 'alter_type', false );
                wp_set_object_terms( (int) $alter, 26147, 'design_type', false );
            }
        } else {
            foreach( $alters as $alter ) {
                $alter = (int) $alter;
                wp_set_object_terms( (int) $alter, [ (int) $typeAlter ], 'alter_type', false );
            }
            wp_set_object_terms( (int) $idDesign, (int) $typeAlter, 'alter_type', false );
            $typeDesign = $generic ? 26149 : 26146;
            wp_delete_object_term_relationships( (int) $idDesign, [ 'design_type' ] );
            wp_set_object_terms( (int) $idDesign, [ $typeDesign ], 'design_type', false );
            foreach( $alters as $alter ) {
                wp_delete_object_term_relationships( (int) $alter, [ 'design_type' ] );
                wp_set_object_terms( (int) $alter, [ $typeDesign ], 'design_type', false );
            }
            if( $generic ) {
                update_post_meta( $idAlter, 'mc_generic', 1 );
                update_post_meta( $idDesign, 'mc_generic', 1 );
            } else {
                delete_post_meta( $idAlter, 'mc_generic' );
                delete_post_meta( $idDesign, 'mc_generic' );
            }
        }
        
        $pathAlter = $args['path_alter'] ?? '';
        if( !empty( $pathAlter ) || $printingChanged ) {
            //if( !empty(self::isAlterFileEmpty($idAlter))) return true;
            MC2_Alter_Functions::updateImages( $idAlter, $pathAlter, $edit );
            if( !empty( $pathPreview ) ) {
                if( file_exists( $pathPreview ) ) unlink( $pathPreview );
            }
        }
        
        return true;
    }
    
    /**
     * @param int $idDesign
     *
     * @return int
     */
    public static function design_altersCheck( $idDesign = 0 ) {
        if( empty( $idDesign ) ) return 0;
        $alters = self::design_alters( $idDesign );
        if( empty( $alters ) ) {
            wp_delete_post( $idDesign, true );
            
            return 0;
        }
        foreach( $alters as $key => $idAlter ) {
            $linkedDesign = self::design( $idAlter );
            if( $linkedDesign == $idDesign ) continue;
            $status = get_post_status( $idAlter );
            if( $status && get_post_status( $idAlter ) != 'trash' ) continue;
            unset( $alters[ $key ] );
        }
        foreach( $alters as $key => $alter ) {
            if( empty( get_post( $alter ) ) || get_post_status( $alter ) == 'trash' ) {
                unset( $alters[ $key ] );
            }
        }
        if( empty( $alters ) || !is_array( $alters ) ) {
            wp_delete_post( $idDesign, true );
            
            return 0;
        }
        update_post_meta( $idDesign, MC2_Design::META_ALTERS, $alters );
        
        return $idDesign;
    }
    
    /**
     * @param array $args
     *
     * @return int|WP_Error
     */
    public static function design_create( $args = [] ) {
        if( empty( $args ) || empty( $args['name'] ) || empty( $args['artist_id'] ) ) return 0;
        $name         = $args['name'];
        $idCreator    = $args['artist_id'];
        $nameAlterist = MC2_User_Functions::displayName( $idCreator );
        if( empty( $nameAlterist ) ) return 0;
        
        $args = [
            'post_title'  => $name,
            'post_name'   => MC2_Vars::generate( 15 ),
            'post_status' => 'pending',
            'post_type'   => 'design',
            'post_author' => $idCreator,
        ];
        
        $author  = is_user_logged_in() ? wp_get_current_user()->ID : 1;
        $author  = !empty( $args['post_author'] ) ? $args['post_author'] : $author;
        $content = !empty( $args['post_content'] ) ? $args['post_content'] : '';
        $title   = !empty( $args['post_title'] ) ? $args['post_title'] : '';
        $slug    = !empty( $args['post_name'] ) ? $args['post_name'] : sanitize_title( $title );
        $status  = !empty( $args['post_status'] ) ? $args['post_status'] : 'pending';
        $tax     = !empty( $args['tax_input'] ) ? $args['tax_input'] : [];
        $meta    = !empty( $args['meta_input'] ) ? $args['meta_input'] : [];
        $type    = !empty( $args['post_type'] ) ? $args['post_type'] : '';
        
        return wp_insert_post( [
                                   'comment_status' => 'closed',
                                   'ping_status'    => 'closed',
                                   'post_author'    => $author,
                                   'post_content'   => $content,
                                   'post_name'      => $slug,
                                   'post_title'     => $title,
                                   'post_status'    => $status,
                                   'post_date_gmt'  => gmdate( 'Y-m-d G:i:s' ),
                                   'tax_input'      => $tax,
                                   'meta_input'     => $meta,
                                   'post_type'      => $type,
                               ] );
    }
    
    /**
     * @param string $bounty
     *
     * @return string
     */
    public static function sanitizeBounty( $bounty = '' ) {
        if( empty( $bounty ) ) return '';
        $bounty = strtolower( $bounty );
        $bounty = trim( $bounty );
        $bounty = MC2_Vars::alphanumericOnly( $bounty );
        
        return $bounty;
    }
    
    public static function updateImages( $idAlter = 0, $pathAlter = '', $edit = true ) {
        if( empty( $idAlter ) ) return false;
        if( empty( $pathAlter ) ) $pathAlter = self::pathOriginalPng( $idAlter );
        $idCreator       = MC2_WP::authorId( $idAlter );
        $creatorUsername = get_the_author_meta( 'user_nicename', $idCreator );
        if( !empty( $pathAlter ) ) {
            if( !$edit ) {
                $creatorPathDir = ABSPATH.'files/alterists/'.$creatorUsername;
                $designPathDir  = $creatorPathDir.'/designs/'.$idAlter;
                if( !is_dir( $creatorPathDir ) ) {
                    mkdir( $creatorPathDir, 0755, true );
                    mkdir( $creatorPathDir.'/designs', 0755, true );
                    mkdir( $creatorPathDir.'/sets', 0755, true );
                }
                if( !is_dir( $designPathDir ) ) {
                    mkdir( $designPathDir, 0755, true );
                }
                
                $serverPngPath = DIR_PRINTS_PNG.'/'.$idAlter.'.png';
                if( !is_dir( DIR_PRINTS_PNG ) ) {
                    mkdir( DIR_PRINTS_PNG, 0755, true );
                }
                copy( $pathAlter, $serverPngPath );
            } else {
                $opendrive  = new MC2_Opendrive();
                $folder     = 'ODdfMTgwNjgxNF9VaFd6Ug';
                $readyFiles = $opendrive->opendriveFolderContent( $folder );
                $readyFiles = $readyFiles['Files'];
                foreach( $readyFiles as $readyFile ) {
                    $name   = $readyFile['Name'];
                    $name   = str_replace( '.pdf', '', $name );
                    $fileId = $readyFile['FileId'];
                    if( $name == $idAlter ) {
                        $opendrive->opendriveFileRemove( $fileId, 'ODdfMTgwNjgxNF9VaFd6Ug' );
                        break;
                    }
                }
                $previousVersion = DIR_PRINTS_PNG.'/'.$idAlter.'.png';
                for( $x = 1; $x <= 100; $x++ ) {
                    $copiedVersion = ABSPATH.'files/versions/'.$idAlter.'-'.$x.'.png';
                    if( !file_exists( $copiedVersion ) ) break;
                }
                copy( $previousVersion, $copiedVersion );
                $previousVersions = MC2_WP::meta( 'mc_previous_versions', $idAlter );
                $versions         = [];
                if( is_array( $previousVersions ) ) {
                    $versions = $previousVersions;
                }
                $copiedReadable = SITE_URL.'/files/versions/'.$idAlter.'-'.$x.'.png';
                $versions[]     = [
                    'file' => $copiedReadable,
                    'date' => date( 'd-m-Y H:i:s', time() ),
                ];
                update_post_meta( $idAlter, 'mc_previous_versions', $versions );
                unlink( $previousVersion );
                $serverPngPath = DIR_PRINTS_PNG.'/'.$idAlter.'.png';
                copy( $pathAlter, $serverPngPath );
                $status = 'verify';
                if( get_post_status( $idAlter ) == 'internal_approved' ) $status = 'internal_verify';
                wp_update_post( [
                                    'ID'          => $idAlter,
                                    'post_status' => $status,
                                ] );
            }
            MC2_Alter_Functions::designProcessor( $idAlter );
            
            return true;
        }
        
        wp_schedule_single_event( time(), 'mc_design_status' );
        
        return false;
    }
    
    /**
     * @param int $idAlter
     *
     * @return string
     */
    public static function pathOriginalPng( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return '';
        $uri = trim( MC2_WP::meta( MC2_Alter::META_FILE_ORIGINAL, $idAlter ) );
        
        return str_replace( get_site_url(), ABSPATH, $uri );
    }
    
    /**
     * @param int  $idProduct
     * @param int  $printing_id
     * @param bool $browsing
     *
     * @return bool
     */
    public static function designProcessor( $idProduct = 0, $printing_id = 0, $browsing = true ) : bool {
        if( empty( $idProduct ) ) return false;
        $full = empty( $printing_id ) && $browsing;
        $time = time();
        
        $alterServerpath = ABSPATH.'/files/prints/png/'.$idProduct.'.png';
        if( empty( $printing_id ) ) $printing_id = MC2_WP::meta( 'mc_linked_printing', $idProduct );
        $printing       = new MC2_Printing( $printing_id );
        $cardServerpath = str_replace( SITE_URL, ABSPATH, $printing->imgPng );
        \MC2_Scryfall::import_card_by_scryfall_id( $printing->scryfall_id );
        if( strpos( $cardServerpath, 'unavailable-card' ) !== false ) return false;
        $idCreator       = MC2_WP::authorId( $idProduct );
        $creatorUsername = get_the_author_meta( 'user_nicename', $idCreator );
        
        /**
         * Lets create and store the images
         */
        $creatorPathDir = ABSPATH.'files/alterists/'.$creatorUsername;
        if( !is_dir( $creatorPathDir ) ) {
            mkdir( $creatorPathDir, 0755, true );
            mkdir( $creatorPathDir.'/designs', 0755, true );
            mkdir( $creatorPathDir.'/sets', 0755, true );
        }
        $designPathDir = $creatorPathDir.'/designs/'.$idProduct;
        if( !is_dir( $designPathDir ) ) mkdir( $designPathDir, 0755, true );
        $dirBrowsing = $creatorPathDir.'/designs/'.$idProduct.'/browsing';
        if( !is_dir( $dirBrowsing ) ) mkdir( $dirBrowsing, 0755, true );
        $creatorPathUri = SITE_URL.'/files/alterists/'.$creatorUsername;
        $designPathUri  = $creatorPathUri.'/designs/'.$idProduct;
        
        /*
         * Original Alter Image
         */
        if( $full ) {
            $serverPngPath = DIR_PRINTS_PNG.'/'.$idProduct.'.png';
            if( $alterServerpath != $serverPngPath ) {
                copy( $alterServerpath, $serverPngPath );
            }
        }
        
        /*
         * Mini Res Alter JPG - 200px		as_lo_res_alter_jpg
         */
        if( $full ) {
            if( strpos( $cardServerpath, '/' ) == 0 && strpos( $cardServerpath, '/var' ) != 0 ) $cardServerpath = ABSPATH.$cardServerpath;
            $filename   = '/'.$idProduct.'-mini-res-combined-'.$time.'.jpg';
            $filePath   = $designPathDir.$filename;
            $fileUrl    = $designPathUri.$filename;
            $alterImage = ImageManagerStatic::make( $alterServerpath );
            $alterImage->resize( 200, 278 );
            $printingImage = ImageManagerStatic::make( $cardServerpath );
            $printingImage->resize( 200, 278 );
            $printingImage->insert( $alterImage, 'top-left', 0, 0 );
            $printingImage->encode( 'jpg' );
            $savePath = $filePath;
            $printingImage->save( $savePath );
            MC2_Images::compress( $savePath );
            update_post_meta( $idProduct, 'mc_mini_res_combined_jpg', $fileUrl );
        }
        
        /*
         * Low Res Alter PNG - 350px		as_lo_res_alter_png
         */
        if( $full ) {
            $filename   = '/'.$idProduct.'-alter-'.$time.'.png';
            $filePath   = $designPathDir.$filename;
            $fileUrl    = $designPathUri.$filename;
            $alterImage = ImageManagerStatic::make( $alterServerpath );
            $alterImage->resize( 350, 488 );
            $savePath = $filePath;
            $alterImage->save( $savePath );
            $alterImage->encode( 'png' );
            MC2_Images::compress( $savePath );
            update_post_meta( $idProduct, 'mc_lo_res_alter_png', $fileUrl );
        }
        
        /*
         * Low Res Combined JPG - 350px		as_lo_res_combined_jpg
         */
        $filename   = $full ? '/'.$idProduct.'-combined-'.$time.'.jpg' : '/browsing/'.$printing_id.'.jpg';
        $filePath   = $designPathDir.$filename;
        $fileUrl    = $designPathUri.$filename;
        $alterImage = ImageManagerStatic::make( $alterServerpath );
        $alterImage->resize( 350, 488 );
        $printingImage = ImageManagerStatic::make( $cardServerpath );
        $printingImage->resize( 350, 488 );
        $printingImage->insert( $alterImage, 'top-left', 0, 0 );
        $printingImage->encode( 'jpg' );
        
        $savePath = $filePath;
        $printingImage->save( $savePath );
        MC2_Images::compress( $savePath );
        if( $full ) {
            update_post_meta( $idProduct, 'mc_lo_res_combined_jpg', $fileUrl );
        } else {
            $printings = MC2_WP::meta( 'mc_images_printings_lo', $idProduct );
            if( empty( $printings ) || !is_array( $printings ) ) $printings = [];
            $printings[] = $printing_id;
            $printings   = array_unique( $printings );
            update_post_meta( $idProduct, 'mc_images_printings_lo', $printings );
        }
        
        /*
         * Low Res Combined PNG - 350px		as_lo_res_combined_png
         */
        if( $full ) {
            $filename   = '/'.$idProduct.'-combined-'.$time.'.png';
            $filePath   = $designPathDir.$filename;
            $fileUrl    = $designPathUri.$filename;
            $alterImage = ImageManagerStatic::make( $alterServerpath );
            $alterImage->resize( 350, 488 );
            $printingImage = ImageManagerStatic::make( $cardServerpath );
            $printingImage->resize( 350, 488 );
            $printingImage->insert( $alterImage, 'top-left', 0, 0 );
            $printingImage->encode( 'png' );
            $savePath = $filePath;
            $printingImage->save( $savePath );
            MC2_Images::compress( $savePath );
            update_post_meta( $idProduct, 'mc_lo_res_combined_png', $fileUrl );
        }
        
        /*
         * High Res Alter PNG - 700px		as_hi_res_alter_png
         */
        if( $full ) {
            $filename   = '/'.$idProduct.'-alter-'.$time.'@2x.png';
            $filePath   = $designPathDir.$filename;
            $fileUrl    = $designPathUri.$filename;
            $alterImage = ImageManagerStatic::make( $alterServerpath );
            $alterImage->resize( 700, 975 );
            $savePath = $filePath;
            $alterImage->encode( 'png' );
            $alterImage->save( $savePath );
            MC2_Images::compress( $savePath );
            update_post_meta( $idProduct, 'mc_hi_res_alter_png', $fileUrl );
        }
        
        /*
         * High Res Combined PNG - 700px		as_hi_res_combined_png
         */
        if( $full ) {
            $filename   = '/'.$idProduct.'-combined-'.$time.'@2x.png';
            $filePath   = $designPathDir.$filename;
            $fileUrl    = $designPathUri.$filename;
            $alterImage = ImageManagerStatic::make( $alterServerpath );
            $alterImage->resize( 700, 975 );
            $printingImage = ImageManagerStatic::make( $cardServerpath );
            $printingImage->resize( 700, 975 );
            $printingImage->insert( $alterImage );
            $printingImage->encode( 'png' );
            $savePath = $filePath;
            $printingImage->save( $savePath );
            MC2_Images::compress( $savePath );
            update_post_meta( $idProduct, 'mc_hi_res_combined_png', $fileUrl );
        }
        
        /*
         * Social Square
         */
        $filename   = $full ? '/'.$idProduct.'-social-'.$time.'.jpg' : '/browsing/'.$printing_id.'-social.jpg';
        $filePath   = $designPathDir.$filename;
        $fileUrl    = $designPathUri.$filename;
        $background = ImageManagerStatic::make( DIR_THEME_IMAGES.'/generation/twitter_card-600.jpg' );
        $alterImage = ImageManagerStatic::make( $alterServerpath );
        $alterImage->resize( 395, 550 );
        $printingImage = ImageManagerStatic::make( $cardServerpath );
        $printingImage->resize( 395, 550 );
        $background->insert( $printingImage, 'top-left', 102, 25 );
        $background->insert( $alterImage, 'top-left', 102, 25 );
        $background->encode( 'jpg' );
        $background->save( $filePath );
        if( $full ) {
            update_post_meta( $idProduct, 'mc_social_square', $fileUrl );
        } else {
            $printings = MC2_WP::meta( 'mc_images_printings_square', $idProduct );
            if( empty( $printings ) || !is_array( $printings ) ) $printings = [];
            $printings[] = $printing_id;
            $printings   = array_unique( $printings );
            update_post_meta( $idProduct, 'mc_images_printings_square', $printings );
        }
        
        return true;
    }
    
    /**
     * @param int $idAlter
     *
     * @return array
     */
    public static function printingsBrowsing( $idAlter = 0 ) {
        if( empty( $idAlter ) ) return [];
        $printings = MC2_WP::meta( 'mc_images_printings_lo', $idAlter );
        if( empty( $printings ) || !is_array( $printings ) ) return [];
        
        return $printings;
    }
    
    /**
     * @param int    $idAlter
     * @param string $itemKey
     *
     * @return string
     */
    public static function nameCart( int $idAlter = 0, string $itemKey = '' ) {
        if( empty( $idAlter ) || empty( $itemKey ) ) return '';
        if( !empty( $name ) ) return $name;
        $printing_id = MC2_Cart_Item_Functions::getMeta( $itemKey, 'mc_printing_id', 0 );
        $printing_id = !empty( $printing_id ) ? $printing_id : MC2_Alter_Functions::printing( $idAlter );
        $printing    = new MC2_Printing( $printing_id );
        
        $name = $idAlter.' - '.$printing->name.' ('.$printing->set_name.' - '.$printing->collector_number.')';
        if( get_post_status( $idAlter ) == 'publish' ) $name = '<a href="'.get_the_permalink( $idAlter ).'">'.$name.'</a>';
        return $name;
    }
    
    /**
     * @param int    $idAlte
     * @param string $itemKey
     *
     * @return string
     */
    public static function nameOrderReview( int $idAlter = 0 ) : string {
        if( empty( $idAlter ) ) return '';
        $nameAlterist = self::getAlteristDisplayName( $idAlter );
        return $idAlter.' - <small>'.$nameAlterist.'</small>';
    }
    
    /**
     * @param $idProduct
     *
     * @return string
     */
    public static function getAlteristDisplayName( $idProduct ) {
        $idCreator = MC2_WP::authorId( $idProduct );
        if( empty( $idCreator ) ) return 0;
        
        return get_the_author_meta( 'display_name', $idCreator );
    }
    
    /**
     * @param int    $idAlter
     * @param string $itemKey
     *
     * @return string
     * @throws Exception
     */
    public static function nameOrder( int $idAlter = 0, string $itemKey = '' ) {
        if( empty( $idAlter ) || empty( $itemKey ) ) return '';
        if( !empty( $name ) ) return $name;
        if( !MC2_Product_Functions::isAlter( $idAlter ) ) return '';
        $printing_id = MC2_Order_Item_Functions::meta( $itemKey, 'mc_printing_id' );
        if( empty( $printing_id ) ) $printing_id = self::printing( $idAlter );
        $itemName  = $idAlter;
        $printing  = new MC2_Printing( $printing_id );
        $name_card = $printing->name;
        if( empty( $name_card ) ) return $itemName;
        $printing = new MC2_Printing( $printing_id );
        $setName  = $printing->set_name;
        $itemName .= ' - '.$name_card;
        
        //$itemName .= !empty($itemName) && !MC2_Mtg_Printing_Functions::is_basic_land($printing_id) ? ' ('.$setName.')' : ' (Frame Era: '.self::getFrameEraReadable($idAlter).')';
        
        return $itemName;
    }
    
    public static function upload() {
        $response = [
            'url'        => '',
            'message'    => '',
            'dimensions' => 1,
        ];
        
        if( !function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH.'wp-admin/includes/file.php' );
        }
        $response['file'] = $uploadedfile = $_FILES['file'];
        
        $upload_overrides        = [ 'test_form' => false ];
        $response['uploads_dir'] = $dirUploads = wp_upload_dir()['path'];
        $response['handle']      = $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        $basename                = str_replace( $dirUploads.'/', '', $movefile['file'] );
        
        $response['file_name'] = sanitize_file_name( basename( $_FILES['file']['name'] ) );
        $response['url']       = $movefile['url'];
        if( $movefile && !isset( $movefile['error'] ) ) {
            $response['message'] = "File Upload Successfully";
        } else {
            $response['message'] = $movefile['error'];
        }
        $response['preview']      = self::previewInUploads( $basename );
        $response['path_alter']   = $dirUploads.'/'.$basename;
        $response['path_preview'] = str_replace( SITE_URL, ABSPATH, $response['preview'] );
        
        [ $width, $height, $type, $attr ] = getimagesize( $response['path_alter'] );
        if( $width != '2980' || $height != '4160' ) $response['dimensions'] = 0;
        $response['width']  = $width;
        $response['height'] = $height;
        
        header( 'Content-Type: application/json' );
        echo json_encode( $response, JSON_UNESCAPED_SLASHES );
        die();
    }
    
    /**
     * @param string $filename
     *
     * @return string|string[]
     */
    public static function previewInUploads( $filename = '' ) {
        $dirUploads = wp_upload_dir()['path'].'/';
        $nicename   = sanitize_file_name( $filename );
        $filename   = pathinfo( $nicename, PATHINFO_FILENAME );
        $original   = $dirUploads.$nicename;
        if( !file_exists( $original ) ) return '';
        $timestamp = filemtime( $original );
        $file      = ImageManagerStatic::make( $original );
        $file->resize( 300, null, function( $constraint ) {
            $constraint->aspectRatio();
        } );
        $file->encode( 'png' );
        $savePath = $dirUploads.'/'.$filename.'-preview-'.$timestamp.'.png';
        $file->save( $savePath );
        wp_schedule_single_event( time() + 600, 'mc_delete_file', [ $savePath ] );
        
        return str_replace( ABSPATH, SITE_URL.'/', $savePath );
    }
    
    /**
     * @param int $idAlter
     * @param int $target
     *
     * @return false|string
     */
    public static function displaySlider( int $idAlter = 0, int $target = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/store/product/components/slider.php' );
        
        return ob_get_clean();
    }
    
    /**
     * @param int $idAlter
     * @param int $target
     *
     * @return false|string
     */
    public static function displayInfo( int $idAlter = 0, int $target = 0 ) {
        ob_start();
        echo '<div class="as-product-info" data-target="'.$target.'">';
        include( DIR_THEME_TEMPLATE_PARTS.'/store/product/components/info.php' );
        echo '</div>';
        
        return ob_get_clean();
    }
    
    /**
     * @return array
     */
    public static function getStatusArray() {
        return [
            'pending',
            'publish',
            'action',
            'verify',
            'internal_verify',
            'internal_action',
            'internal_approved',
        ];
    }
    
    /**
     * @param int $idAlter
     */
    public static function clean( int $idAlter = 0 ) {
        if( empty( $idAlter ) ) return;
        $idDesign = self::design( $idAlter );
        if( empty( $idDesign ) ) return;
        $typeDesign = self::design_type( $idDesign, 'key' );
        if( has_term( 26164, 'alter_type', $idAlter ) ) $typeDesign = 'frame';
        $printing_id = self::printing( $idAlter );
        if( empty( $printing_id ) ) return;
        $sku = 'altslv-'.$idAlter;
        update_post_meta( $idAlter, '_sku', $sku );
        
        $printings   = self::printings( $idAlter );
        $printings[] = $printing_id;
        $printings   = array_unique( $printings );
        MC2_Alter_Functions::frameSync( $idAlter );
        
        if( $typeDesign != 'frame' ) {
            update_post_meta( $idAlter, 'mc_additional_printings', $printings );
            $idCard = MC2_Mtg_Printing_Functions::getCardId( $printing_id );
            wp_set_object_terms( $idAlter, [ $idCard ], 'mtg_card', false );
            foreach( $printings as $printing ) {
                $setName   = MC2_Mtg_Printing_Functions::setCode( $printing );
                $objectSet = get_term_by( 'slug', $setName, 'mtg_set' );
                if( empty( $objectSet ) ) {
                    $setName   = MC2_Mtg_Printing_Functions::setCode( $printing );
                    $objectSet = get_term_by( 'name', $setName, 'mtg_set' );
                    if( empty( $objectSet ) ) continue;
                }
                $idSet = $objectSet->term_id;
                wp_set_object_terms( $idAlter, [ $idSet ], 'mtg_set', false );
            }
            $printingsDesign = get_post_meta( $idDesign, 'mc_printings', true );
            if( empty( $printingsDesign ) ) $printingsDesign = [];
            $printingsDesign = array_merge( $printingsDesign, $printings );
            $printingsDesign = array_unique( $printingsDesign );
            foreach( $printingsDesign as $printingDesign ) {
                $printing  = new MC2_Printing( $printing_id );
                $setName   = $printing->set_name;
                $objectSet = get_term_by( 'name', $setName, 'mtg_set' );
                if( empty( $objectSet ) ) continue;
                $idSet  = $objectSet->term_id;
                $idCard = MC2_Mtg_Printing_Functions::getCardId( $printingDesign );
                wp_set_object_terms( $idDesign, [ $idCard ], 'mtg_card', true );
                wp_set_object_terms( $idDesign, [ $idSet ], 'mtg_set', true );
            }
        }
        
        if( $typeDesign == 'frame' ) {
            wp_set_object_terms( $idAlter, [ 26147 ], 'design_type', false );
            wp_set_object_terms( $idDesign, [ 26147 ], 'design_type', false );
            wp_set_object_terms( $idAlter, [ 26164 ], 'alter_type', false );
            wp_set_object_terms( $idDesign, [ 26164 ], 'alter_type', false );
            
            return;
        }
        $typeAlter = wp_get_object_terms( $idAlter, 'alter_type' );
        
        if( is_array( $typeAlter ) ) {
            $typeAlter = $typeAlter[0];
            $typeAlter = $typeAlter->term_id;
        } else {
            MC2_Alter_Functions::pullFromStore( $idAlter );
            
            return;
        }
        
        wp_set_object_terms( $idAlter, [ $typeAlter ], 'alter_type', false );
        wp_set_object_terms( $idDesign, [ $typeAlter ], 'alter_type', false );
        
        $generic = MC2_WP::meta( 'mc_generic', $idDesign );
        
        if( $generic ) {
            update_post_meta( $idAlter, 'mc_generic', 1 );
            update_post_meta( $idDesign, 'mc_generic', 1 );
            wp_set_object_terms( $idAlter, [ 26149 ], 'design_type', false );
            wp_set_object_terms( $idDesign, [ 26149 ], 'design_type', false );
        } else {
            delete_post_meta( $idAlter, 'mc_generic' );
            delete_post_meta( $idDesign, 'mc_generic' );
            wp_set_object_terms( $idAlter, [ 26146 ], 'design_type', false );
            wp_set_object_terms( $idDesign, [ 26146 ], 'design_type', false );
        }
        
        $alters  = self::design_alters( $idDesign );
        $publish = true;
        foreach( $alters as $alter ) {
            if( get_post_status( $alter ) == 'publish' ) continue;
            $publish = false;
        }
        if( !$publish || get_post_status( $idDesign ) == 'publish' ) return;
        wp_update_post( [
                            'ID'          => $idDesign,
                            'post_status' => 'publish',
                        ] );
    }
    
    /**
     * @param null $design
     *
     * @return array
     */
    public static function printings( $design = null ) : array {
        if( empty( $design ) ) return [ self::printing( $design ) ];
        if( !is_object( $design ) ) $design = new MC2_Alter( $design );
        if( empty( $design ) ) return [ self::printing( $design ) ];
        
        return [ new MC2_Printing( $design->linkedPrintings ) ?? self::printing( $design ) ];
    }
    
    public static function pullFromStore( int $idAlter ) {
        if( empty( $idAlter ) ) return;
        wp_update_post( [
                            'ID'          => $idAlter,
                            'post_status' => 'action',
                        ] );
        $idDesign = self::design( $idAlter );
        wp_update_post( [
                            'ID'          => $idDesign,
                            'post_status' => 'pending',
                        ] );
        if( empty( $idDesign ) ) return;
    }
    
    /**
     * @param int $alter_id
     *
     * @return array|mixed
     */
    public static function additionalPrintings( $alter_id = 0 ) {
        return get_post_meta( $alter_id, 'mc_additional_printings', true ) ?? [];
    }
    
    /**
     * @param $status
     *
     * @return string
     */
    public static function statusConverter( $status ) {
        switch( $status ) {
            case 'approved':
            case 'publish' :
                return 'Approved';
            case 'pending' :
                return 'Pending';
            case 'verify' :
                return 'Awaiting Verification';
            case 'action' :
                return 'Action Required';
            case 'flagged' :
                return 'Flagged';
            case 'rejected' :
                return 'Removed';
            case 'internal_verify' :
                return 'Internally Verified';
            case 'internal_approved' :
                return 'Internally Approved';
            default :
                return '';
        }
    }
    
    /**
     * @param string $id
     * @param string $status
     *
     * @return string
     */
    public static function getSleeveStatus( $id = '', $status = '' ) {
        if( $id == '' ) return '';
        if( $status == '' ) $status = get_post_status( $id );
        switch( $status ) {
            case 'approved':
            case 'publish' :
                return 'Approved';
            case 'pending' :
                return 'Pending';
            case 'verify' :
                return 'Awaiting Verification';
            case 'action' :
                return 'Action Required';
            case 'flagged' :
                return 'Flagged';
            case 'rejected' :
                return 'Removed';
            case 'internal_verify' :
                return 'Internally Verified';
            case 'internal_approved' :
                return 'Internally Approved';
            default :
                return '';
        }
    }
    
    /**
     * @param int $artist_id
     *
     * @return array|int[]|string|string[]|WP_Error|WP_Term[]
     */
    public static function getDesignGroupsByArtist( $artist_id = 0 ) {
        if( empty( $artist_id ) ) return [];
        $args = [
            'taxonomy'   => 'design_group',
            'meta_query' => [
                [
                    'key'     => 'mc_artist',
                    'compare' => '=',
                    'value'   => $artist_id,
                ],
            ],
            'orderby'    => 'rand',
        ];
        
        return get_terms( $args );
    }
    
    /**
     * @param int $artist_id
     *
     * @return array
     */
    public static function getAltersFromDesignGroupsByArtist( $artist_id = 0 ) : array {
        $design_groups = self::getDesignGroupsByArtist( $artist_id );
        if( empty( $design_groups ) ) return [];
        $design_groups = array_slice( $design_groups, 0, 12 );
        $results       = [];
        foreach( $design_groups as $design_group ) {
            $alters = get_term_meta( $design_group->term_id, 'mc_variations', true );
            if( empty( $alters ) ) continue;
            shuffle( $alters );
            foreach( $alters as $alter ) {
                if( get_post_status( $alter ) != 'publish' ) continue;
                $results[] = $alter;
                break;
            }
        }
        
        return $results;
    }
    
    public static function align_alters_with_cards() {
        global $wpdb;
        
        $table_name = "{$wpdb->prefix}mtg_cards";
        
        $args   = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => MC2_Statuses::keys(),
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => 'alter',
                ],
            ],
            'fields'         => 'ids',
        ];
        $alters = get_posts( $args );
        
        foreach( $alters as $alter_id ) {
            $printing_id = get_post_meta( $alter_id, 'mc_linked_printing', true );
            if( empty( $printing_id ) ) continue;
            
            $scryfall_id = get_post_meta( $printing_id, 'mc_scryfall_id', true );
            if( empty( $scryfall_id ) ) $scryfall_id = get_post_meta( $printing_id, 'mc_id', true );
            if( empty( $scryfall_id ) ) continue;
            $result = $wpdb->get_row( "SELECT * FROM $table_name WHERE scryfall_id = '$scryfall_id' AND face = '1' " );
            if( empty( $result ) ) continue;
            update_post_meta( $alter_id, 'mc_card', $result->ID );
        }
    }
    
}