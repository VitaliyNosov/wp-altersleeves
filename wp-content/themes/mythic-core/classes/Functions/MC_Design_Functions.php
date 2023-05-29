<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Post_Type_Functions;
use Mythic_Core\Objects\MC_Design;
use Mythic_Core\System\MC_Statuses;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Vars;

/**
 * Class MC_Design_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Design_Functions extends MC_Post_Type_Functions {
    
    public static $post_type = 'design';
    
    /**
     *
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
            'taxonomy' => 'product_group',
            'field'    => 'slug',
            // @Todo update to design - alter is a kind of design
            'terms'    => [ 'alter' ],
            'compare'  => 'IN',
        ];
        if( $all ) $args['post_status'] = MC_Statuses::keys();
        
        return get_posts( $args );
    }
    
    const NAME          = 'design';
    const READABLE_NAME = 'Design';
    
    /**
     * @return array
     */
    public static function getPostTypeSettings() : array {
        return [
            'name'                => self::NAME,
            'label'               => self::READABLE_NAME.'s',
            'label_singular'      => self::READABLE_NAME,
            'public'              => true,
            'show_ui'             => true,
            'has_archive'         => false,
            'supports'            => [ 'author', 'title' ],
            'hierarchical'        => false,
            'exclude_from_search' => true,
        ];
    }
    

    /**
     * @param string $search_term
     *
     * @return array
     */
    public static function search_designs( $search_term = '' ) : array {
        $args = [
            'post_type'      => 'design',
            'post_status'    => [ 'pending', 'publish' ],
            'posts_per_page' => 18,
            'fields'         => 'ids',
        ];
        if( !MC_User_Functions::isAdmin() ) {
            $args['author'] = wp_get_current_user()->ID;
        }
        if( is_numeric( $search_term ) ) {
            $args['p'] = $search_term;
        } else {
            $args['s'] = $search_term;
        }
        $designs = get_posts( $args );
        if( empty( $designs ) && is_numeric( $search_term ) ) {
            $design_id = MC_Alter_Functions::design( $search_term );
            $designs   = !empty( $design_id ) ? [ $design_id ] : [];
        }
        $results = [];
        foreach( $designs as $key => $design_id ) {
            $results[] = [
                'id'         => $design_id,
                'name'       => get_the_title( $design_id ),
                'generic'    => self::isGeneric( $design_id ),
                'variations' => self::alters( $design_id ),
                'preview'    => self::imageCombined( $design_id ),
            ];
        }
        
        return $results;
    }
    
    /**
     * @param int $idDesign
     *
     * @return bool
     */
    public static function isGeneric( $idDesign = 0 ) : bool {
        if( !self::potentiallyGeneric( $idDesign ) ) return false;
        if( !empty( get_post_meta( $idDesign, 'mc_generic', true ) ) ) return true;
        
        return false;
    }
    
    /**
     * @param int $idDesign
     *
     * @return bool
     */
    public static function potentiallyGeneric( $idDesign = 0 ) : bool {
        if( empty( $idDesign ) ) return false;
        foreach( self::genericTypes() as $generic_type ) {
            if( has_term( $generic_type, 'alter_type', $idDesign ) ) return true;
        }
        
        return false;
    }
    
    /**
     * @return int[]
     */
    public static function genericTypes() : array {
        return [ 26161, 26158, 26159, 35867, 35868 ];
    }
    
    /**
     * @param int $idDesign
     *
     * @return array
     */
    public static function alters( $idDesign = 0 ) {
        $idDesign = self::validate( $idDesign );
        if( empty( $idDesign ) ) return [];
        $alters = MC_WP::meta( MC_Design::META_ALTERS, $idDesign );
        if( empty( $alters ) || !is_array( $alters ) ) return [];
        
        return $alters;
    }
    
    /**
     * @param int $id
     *
     * @return false|int
     */
    public static function validate( $id = 0 ) {
        if( empty( $id ) ) $id = MC_WP::currentId();
        if( get_post_type( $id ) == 'design' ) return $id;
        if( get_post_type( $id ) == 'product' ) {
            $idProduct = MC_Alter_Functions::design( $id );
            if( !empty( $idProduct ) ) return $idProduct;
        }
        
        return 0;
    }
    
    /**
     * @param int $idDesign
     * @param int $idAlter
     *
     * @return string
     */
    public static function imageCombined( $idDesign = 0, $idAlter = 0 ) {
        if( empty( $idDesign ) ) return AS_URI_IMG.'/unavailable-card.png';
        if( empty( $idAlter ) || !in_array( $idAlter, self::alters( $idDesign ) ) ) {
            $idAlter = self::alter( $idDesign );
        }
        
        return MC_Alter_Functions::getCombinedImage( $idAlter );
    }
    
    /**
     * @param int $idDesign
     *
     * @return int
     */
    public static function alter( $idDesign = 0 ) {
        $idDesign = self::validate( $idDesign );
        if( empty( $idDesign ) ) return 0;
        $variations = self::alters( $idDesign );
        if( empty( $variations ) ) return 0;
        if( is_array( $variations ) ) return $variations[0];
        if( is_numeric( $variations ) ) return $variations;
        
        return 0;
    }
    
    /**
     * @return int|WP_Error
     */
    public static function create_empty_design() {
        if( !is_user_logged_in() ) return 0;
        $temp_name = MC_Vars::generate( 10 );
        
        return wp_insert_post( [
                                   'comment_status' => 'closed',
                                   'ping_status'    => 'closed',
                                   'post_author'    => wp_get_current_user()->ID,
                                   'post_content'   => '',
                                   'post_name'      => $temp_name,
                                   'post_title'     => $temp_name,
                                   'post_status'    => 'pending',
                                   'post_date_gmt'  => gmdate( 'Y-m-d G:i:s' ),
                                   'post_type'      => 'design',
                               ] );
    }
    
    /**
     * @return int
     */
    public static function _get() {
        $idAlter  = isset( $_GET['alter_id'] ) ? $_GET['alter_id'] : '';
        $idDesign = isset( $_GET['design_id'] ) ? $_GET['design_id'] : '';
        if( empty( $idAlter ) && empty( $idDesign ) ) return self::idFromQueriedObject();
        
        $idDesign = MC_Alter_Functions::design( $idAlter );
        if( !empty( $idDesign ) ) return $idDesign;
        
        return 0;
    }
    
    public static function idFromQueriedObject() {
        $object = get_queried_object();
        if( empty( $object ) || is_object( $object ) ) return 0;
        $idObject = $object->ID;
        if( get_post_type( $idObject ) == 'design' ) return $idObject;
        if( !get_post_type( $idObject ) == 'product' ) return 0;
        $idDesign = MC_Alter_Functions::design( $idObject );
        if( empty( $idDesign ) ) return 0;
        
        return $idDesign;
    }
    
    /**
     * @param int    $idDesign
     * @param string $result
     *
     * @return array|string
     */
    public static function typeAlter( $idDesign = 0, $result = 'name' ) {
        if( empty( $idDesign ) ) $idDesign = self::_get();
        if( empty( $idDesign ) ) return '';
        $alters = self::alters( $idDesign );
        if( empty( $alters ) || !is_array( $alters ) ) return '';
        $idAlter = $alters[0];
        if( !is_numeric( $idAlter ) ) return '';
        
        return MC_Alter_Functions::type( $idAlter, $result );
    }
    
    /**
     * @param int $idDesign
     *
     * @return bool|mixed|string
     */
    public static function connected( $idDesign = 0 ) {
        if( empty( $idDesign ) ) $idDesign = self::_get();
        $connected = MC_WP::meta( MC_Design::META_LINKED_DESIGNS, $idDesign );
        if( empty( $connected ) ) return [];
        
        return $connected;
    }
    
    /**
     * @param int $idDesign
     *
     * @return int
     */
    public static function altersCheck( $idDesign = 0 ) {
        if( empty( $idDesign ) ) return 0;
        $alters = self::alters( $idDesign );
        if( empty( $alters ) ) {
            wp_delete_post( $idDesign, true );
            
            return 0;
        }
        foreach( $alters as $key => $idAlter ) {
            $linkedDesign = MC_Alter_Functions::design( $idAlter );
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
        update_post_meta( $idDesign, MC_Design::META_ALTERS, $alters );
        
        return $idDesign;
    }
    
    /**
     * @return array
     */
    public static function taxStatus() {
        return [
            'key'   => 'design_status',
            'posts' => [ 'design' ],
            'label' => 'Alter Status',
        ];
    }
    
    public static function maintenanceRemoveIncorrectAlterConnections() {
        $designs = self::all();
        foreach( $designs as $idDesign ) {
            $idAlter     = self::alter( $idDesign );
            $proxyDesign = MC_Alter_Functions::design( $idAlter );
            if( $idDesign == $proxyDesign ) continue;
            wp_delete_post( $idDesign );
        }
    }
    
    public static function all( $publishOnly = true ) {
        $status      = $publishOnly ? 'publish' : [ 'publish', 'pending' ];
        $argsDesigns = [
            'post_type'      => 'design',
            'posts_per_page' => -1,
            'post_status'    => $status,
            'fields'         => 'ids',
        ];
        
        return get_posts( $argsDesigns );
    }
    
    /**
     * @param int $idDesign
     * @param int $idCard
     * @param int $idSet
     *
     * @return int|mixed|string
     */
    public static function alterByCardAndSet( $idDesign = 0, $idCard = 0, $idSet = 0 ) {
        $idDesign = self::validate( $idDesign );
        if( empty( $idDesign ) ) return 0;
        $idCard = isset( $_GET['card_id'] ) ? $_GET['card_id'] : $idCard;
        $idSet  = isset( $_GET['set_id'] ) ? $_GET['set_id'] : $idSet;
        if( empty( $idCard ) || empty( $idSet ) ) return self::alter( $idDesign );
        $variations = self::alters( $idDesign );
        
        // @todo finish this
        return 0;
    }
    
    /**
     * @param int $idDesign
     * @param int $idPrinting
     *
     * @return int|mixed|string
     */
    public static function alterByPrinting( $idDesign = 0, $idPrinting = 0 ) {
        $idDesign = self::validate( $idDesign );
        if( empty( $idDesign ) || empty( $idPrinting ) ) return 0;
        $framecode  = MC_Mtg_Printing_Functions::codeFromId( $idPrinting );
        $variations = self::alters( $idDesign );
        if( !is_array( $variations ) || empty( $variations ) ) return 0;
        foreach( $variations as $variation ) {
            if( has_term( $framecode, 'frame_code', $variation ) ) return $variation;
        }
        
        return $variations[0];
    }
    
    public static function maintenanceStatus() {
        $designs = self::all( false );
        foreach( $designs as $idDesign ) {
            self::updateDesignStatus( $idDesign );
        }
    }
    
    public static function updateDesignStatus( $idDesign = 0 ) {
        if( empty( $idDesign ) ) return;
        $publish = true;
        $alters  = self::alters( $idDesign );
        foreach( $alters as $key => $idAlter ) {
            if( get_post( $idAlter ) == null ) {
                unset( $alters[ $key ] );
                continue;
            }
            if( get_post_status( $idAlter ) !== 'publish' || in_array( $idAlter, MC_Product_Functions::snapboltIds() ) ) {
                $publish = false;
            }
        }
        if( empty( $alters ) ) {
            wp_delete_post( $idDesign, true );
            
            return;
        }
        update_post_meta( $idDesign, MC_Design::META_LINKED_DESIGNS, $alters );
        if( !$publish ) {
            wp_update_post( [
                                'ID'          => $idDesign,
                                'post_status' => 'pending',
                            ] );
        } else {
            wp_update_post( [
                                'ID'          => $idDesign,
                                'post_status' => 'publish',
                            ] );
        }
        
        $mostRecent = max( $alters );
        update_post_meta( $idDesign, 'mc_most_recent', $mostRecent );
    }
    
    /**
     * @param int $idDesign
     * @param int $target
     *
     * @return false|string
     */
    public static function displaySlider( int $idDesign = 0, int $target = 0 ) {
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
    public static function displayInfo( int $idDesign = 0, int $target = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/store/product/components/info.php' );
        
        return ob_get_clean();
    }
    
    public static function modalFramePrintingSwitcher() {
        ob_start();
        include DIR_THEME_TEMPLATE_PARTS.'/store/product/components/frame-printing-switcher/modal.php';
        echo ob_get_clean();
    }
    
    /**
     * @param int $id
     *
     * @return false|WP_Post|null
     */
    public static function trashItem( $id = 0 ) {
        if( $id == 0 ) return false;
        $force = false;
        if( get_post_type( $id ) == 'design' ) {
            $linkedVariations = MC_WP::meta( MC_Design::META_ALTERS, $id );
            foreach( $linkedVariations as $linkedVariation ) {
                $variationMetas = [
                    'mc_linked_design',
                    'mc_frame_code',
                    'mc_validated_color_matching',
                    'mc_validated_alignment',
                    'mc_validated_file',
                ];
                foreach( $variationMetas as $variationMeta ) {
                    delete_post_meta( $linkedVariation, $variationMeta );
                }
                wp_set_post_terms( $linkedVariation, [], 'mtg_frame' );
                $force = true;
            }
        }
        
        return wp_delete_post( $id, $force );
    }
    
    public static function designStatus() {
        $argsDesigns = [
            'post_type'      => 'design',
            'posts_per_page' => -1,
            'post_status'    => [ 'publish', 'pending' ],
            'fields'         => 'ids',
        ];
        $designs     = get_posts( $argsDesigns );
        foreach( $designs as $idDesign ) {
            self::updateDesignStatus( $idDesign );
        }
    }
    
    /**
     * @return string
     */
    public static function fieldDesignName() {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/name.php' );
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/type.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $idDesign
     *
     * @return false|string
     */
    public static function fieldDesignSearch( $idDesign = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/search.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param string $search_term
     *
     * @return false|string
     */
    public static function getFieldSelect( $search_term = '' ) {
        if( empty( $search_term ) ) return '';
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/select.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $idDesign
     * @param int $idAlter
     *
     * @return string
     */
    public static function getFieldSelected( $idDesign = 0, $idAlter = 0 ) {
        if( empty( $idDesign ) ) return '';
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/selected.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $idDesign
     * @param int $idSelected
     *
     * @return string
     */
    public static function getResultSearch( $idDesign = 0, $idSelected = 0 ) {
        if( empty( $idDesign ) ) return '';
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/items/design/management-selection.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param string $search_term
     *
     * @return array
     */
    public static function searchByTerm( $search_term = '' ) {
        if( empty( $search_term ) ) return [];
        $search_term = utf8_encode( trim( $search_term ) );
        $argsInit    = [
            'post_type'      => 'design',
            'post_status'    => [ 'pending', 'publish' ],
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];
        if( !MC_User_Functions::isAdmin() ) $argsInit['author'] = wp_get_current_user()->ID;
        if( is_numeric( $search_term ) ) {
            $argsInit['p'] = $search_term;
            $designs       = get_posts( $argsInit );
        } else {
            $argsInit['s'] = $search_term;
            $designs       = get_posts( $argsInit );
        }
        $designs = array_unique( $designs );
        if( count( $designs ) > 14 ) $designs = array_slice( $designs, 0, 14 );
        
        return $designs;
    }
    
    /**
     * @param string $typeDesign
     *
     * @return string
     */
    public static function fieldLandCard( $typeDesign = '' ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/land-card.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $idPrinting
     *
     * @return string
     */
    public static function fieldPrintingSelection( $idPrinting = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/printing.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $idDesign
     *
     * @return string
     */
    public static function fieldAlterType( $idDesign = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/type.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param string $typeDesign
     * @param int    $idAlter
     *
     * @return string
     */
    public static function fieldCardSearch( $typeDesign = '', $idAlter = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/card-search.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param array  $results
     * @param string $search_term
     *
     * @return false|string
     */
    public static function fieldCardResults( $results = [], $search_term = '', $x = 0 ) {
        if( empty( $results ) ) return '';
        ob_start();
        if( $x ) {
            include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/card-results-printing.php' );
        } else {
            include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/card-results.php' );
        }
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $idCard
     * @param int $x
     *
     * @return string
     */
    public static function cardResult( $idCard = 0, $x = 0 ) {
        if( empty( $idCard ) ) return '';
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/items/card/name.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param array $results
     * @param       $idCard
     *
     * @return false|string
     */
    public static function fieldSetSelection( array $results, $idCard, $x = 0 ) {
        if( empty( $idCard ) ) return '';
        ob_start();
        if( !empty( $x ) ) {
            include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/set-selection-art-replacement.php' );
        } else {
            include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/set-selection.php' );
        }
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @return string
     */
    public static function fieldGenericAlter( $idDesign = 0 ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/generic.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * /**
     * @param string $type
     *
     * @return string
     */
    public static function textMultiplePrintings( $type = '' ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/texts/printings.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param string $type
     *
     * @return string
     */
    public static function fieldMultiplePrintings( $type = '' ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/printings.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @return string
     */
    public static function fieldTags() {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/design/tags.php' );
        self::fieldsLegal();
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @return string
     */
    public static function fieldsLegal() {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/legal.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @return string
     */
    public static function fieldConfirm() {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/creator/management/form-fields/alter/confirm.php' );
        
        return utf8_encode( ob_get_clean() );
    }
    
    /**
     * @param int $id
     */
    public static function frameSync( $id = 0 ) {
        $type = get_post_type( $id );
        if( $type != 'product' && $type != 'design' ) return;
        if( $type == 'product' ) {
            $id = MC_Alter_Functions::design( $id );
            if( empty( $id ) ) return;
        }
        $alters = self::alters( $id );
        if( empty( $alters ) ) return;
        wp_delete_object_term_relationships( $id, 'frame_code' );
        foreach( $alters as $alter ) MC_Alter_Functions::frameSync( $alter );
    }
    
}
