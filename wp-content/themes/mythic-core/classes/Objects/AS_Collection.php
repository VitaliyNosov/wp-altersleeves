<?php

namespace Mythic_Core\Objects;

use Intervention\Image\ImageManagerStatic;
use MC_Alter_Functions;
use MC_Design_Functions;
use MC_Product_Functions;
use MC_User;
use MC_Vars;
use MC_WP;
use phpmailerException;
use WP_Error;

/**
 * Class Collection
 *
 * @package Mythic_Core\Objects
 */
class Collection {
    
    public function __construct() {
    }
    
    /**
     * @param int $idCollection
     *
     * @return array
     */
    public static function designs( int $idCollection = 0 ) {
        if( empty( $idCollection ) ) return [];
        $singles = self::singles( $idCollection );
        foreach( $singles as $key => $single ) {
            $id   = $single['default_id'];
            $type = get_post_type( $id );
            switch( $type ) {
                case 'design' :
                    $singles[ $key ] = $id;
                    break;
                case ( 'product' && MC_Product_Functions::isAlter( $id ) ) :
                    $idAlter  = $id;
                    $idDesign = MC_Alter_Functions::design( $idAlter );
                    if( empty( $idDesign ) ) return [];
                    $singles[ $key ] = $idDesign;
                    break;
                default :
                    return [];
            }
        }
        return $singles;
    }
    
    /**
     * @param int $idCollection
     *
     * @return array
     */
    public static function singles( int $idCollection = 0 ) {
        if( empty( $idCollection ) ) return [];
        $data = MC_WP::meta( '_bto_data', $idCollection );
        return !empty( $data ) ? $data : [];
    }
    
    public static function alteristHasCollections( $userId = 0 ) {
        if( empty( $userId ) ) {
            if( !is_user_logged_in() ) {
                return false;
            }
            $userId = wp_get_current_user()->ID;
        }
        $args = [
            'post_type'      => 'product',
            'author'         => $userId,
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'tax_query'      => [
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'composite',
                ],
            ],
            'fields'         => 'ids',
        ];
        if( MC_User::isAdmin() ) unset( $args['author'] );
        $collections = get_posts( $args );
        if( empty( $collections ) ) return false;
        return true;
    }
    
    public static function _get() {
        $idCollection = isset( $_GET['collection_id'] ) ? $_GET['collection_id'] : '';
        if( empty( $idCollection ) ) return 0;
        return $idCollection;
    }
    
    /**
     * @param array $designs
     * @param int   $productId
     *
     * @return false|int|WP_Error
     * @throws phpmailerException
     */
    public static function manageCompositeSet( $designs = [], $productId = 0 ) {
        if( empty( $designs ) ) return false;
        foreach( $designs as $idDesign ) {
            $alters = MC_Design_Functions::alters( $idDesign );
            if( empty( $alters ) ) return false;
        }
        $alteristId          = MC_WP::authorId( $designs[0] );
        $alteristDisplayName = get_the_author_meta( 'display_name', $alteristId );
        
        $countDesigns = count( $designs );
        if( empty( $productId ) || !MC_WP::exists( $productId ) ) {
            $title     = $countDesigns.' Sleeve collection by '.$alteristDisplayName;
            $slug      = sanitize_title( MC_Vars::generate( 10 ) );
            $productId = wp_insert_post(
                [
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                    'post_author'    => $alteristId,
                    'post_title'     => $title,
                    'post_name'      => $slug,
                    'post_status'    => 'pending',
                    'post_parent'    => '',
                    'post_type'      => 'product',
                    'post_date_gmt'  => gmdate( 'Y-m-d G:i:s' ),
                ]
            );
        }
        
        switch( $countDesigns ) {
            case 2 :
                $type = 31;
                break;
            case 3 :
                $type = 26;
                break;
            case 4 :
                $type = 27;
                break;
            case 5 :
                $type = 28;
                break;
            case 6 :
                $type = 29;
                break;
        }
        if( isset( $type ) ) wp_set_post_terms( $productId, $type, 'set_type', true );
        $sku = 'collection-'.$productId;
        wp_set_object_terms( $productId, 'composite', 'product_type' );
        update_post_meta( $productId, '_sku', $sku );
        $credits = $countDesigns;
        
        $wooItems = [
            [ 'key' => '_backorders', 'value' => 'no' ],
            [ 'key' => '_bto_add_to_cart_form_location', 'value' => 'default' ],
            [ 'key' => '_bto_base_price', 'value' => '0' ],
            [ 'key' => '_bto_base_regular_price', 'value' => '0' ],
            [ 'key' => '_bto_edit_in_cart', 'value' => 'no' ],
            [ 'key' => '_bto_scenario_data', 'value' => 'a:0:{}' ],
            [ 'key' => '_bto_shop_price_calc', 'value' => 'defaults' ],
            [ 'key' => '_bto_sold_individually', 'value' => 'product' ],
            [ 'key' => '_bto_style', 'value' => 'single' ],
            [ 'key' => '_download_expiry', 'value' => '-1' ],
            [ 'key' => '_download_limit', 'value' => '-1' ],
            [ 'key' => '_downloadable', 'value' => 'no' ],
            [ 'key' => '_edit_last', 'value' => '1' ],
            [ 'key' => '_manage_stock', 'value' => 'no' ],
            [ 'key' => '_price', 'value' => '0' ],
            [ 'key' => '_product_version', 'value' => '3.7.0' ],
            [ 'key' => '_regular_price', 'value' => '0' ],
            [ 'key' => '_sold_individually', 'value' => 'no' ],
            [ 'key' => '_stock', 'value' => 'NULL' ],
            [ 'key' => '_stock_status', 'value' => 'instock' ],
            [ 'key' => '_tax_status', 'value' => 'taxable' ],
            [ 'key' => '_virtual', 'value' => 'no' ],
            [ 'key' => '_wc_average_rating', 'value' => '0' ],
            [ 'key' => '_wc_review_count', 'value' => '0' ],
            [ 'key' => '_wc_sw_max_price', 'value' => '0' ],
            [ 'key' => '_credits_amount', 'value' => $credits ],
        ];
        foreach( $wooItems as $wooItem ) {
            update_post_meta( $productId, $wooItem['key'], $wooItem['value'] );
        }
        
        $bto     = [];
        $time    = time();
        $publish = true;
        foreach( $designs as $key => $idDesign ) {
            if( get_post_status( $idDesign ) != 'publish' ) $publish = false;
            $alters = as_design_alters( $idDesign );
            if( !is_array( $alters ) ) $alters = [ $alters ];
            $bto[ $time ] = self::btoPrep( $productId, $alters, $time, $key );
            $time++;
        }
        update_post_meta( $productId, '_bto_data', $bto );
        
        self::initImageRunner( $productId );
        if( $publish ) {
            wp_update_post( [
                                'ID'          => $productId,
                                'post_status' => 'publish',
                            ] );
        }
        return $productId;
    }
    
    /**
     * @param string $productId
     * @param string $alters
     * @param string $time
     * @param string $position
     *
     * @return array
     */
    public static function btoPrep( $productId = '', $alters = '', $time = '', $position = '' ) {
        if( empty( $alters ) || empty( $productId ) ) return [];
        if( $time == '' ) $time = rand( 1, 999999 );
        
        return [
            'query_type'               => 'product_ids',
            'assigned_ids'             => $alters,
            'selection_mode'           => 'dropdowns',
            'default_id'               => $alters[0],
            'title'                    => MC_Vars::generate( 10 ),
            'description'              => '',
            'thumbnail_id'             => '',
            'quantity_min'             => 1,
            'quantity_max'             => 1,
            'discount'                 => '',
            'priced_individually'      => 'no',
            'shipped_individually'     => 'no',
            'optional'                 => 'no',
            'display_prices'           => 'absolute',
            'select_action'            => 'view',
            'hide_product_title'       => 'no',
            'hide_product_description' => 'no',
            'hide_product_thumbnail'   => 'no',
            'hide_product_price'       => 'no',
            'hide_subtotal_product'    => 'no',
            'hide_subtotal_cart'       => 'no',
            'hide_subtotal_orders'     => 'no',
            'show_orderby'             => 'no',
            'show_filters'             => 'no',
            'position'                 => $position,
            'component_id'             => $time,
            'composite_id'             => $productId,
            'pagination_style'         => 'classic',
        ];
    }
    
    public static function initImageRunner( $id = '' ) {
        if( $id == '' ) return false;
        $alteristId       = get_post_field( 'post_author', $id );
        $alteristUsername = get_the_author_meta( 'user_nicename', $alteristId );
        
        // Set Background
        $alteristPathDir = ABSPATH.'files/alterists/'.$alteristUsername;
        if( !is_dir( $alteristPathDir ) ) {
            mkdir( $alteristPathDir, 0755, true );
            mkdir( $alteristPathDir.'/designs', 0755, true );
            mkdir( $alteristPathDir.'/sets', 0755, true );
        }
        if( !is_dir( $alteristPathDir.'/sets/'.$id ) ) {
            mkdir( $alteristPathDir.'/sets/'.$id, 0777, true );
        }
        
        $savePath     = $alteristPathDir.'/sets/'.$id.'/'.$id.'-full.png';
        $savePathMini = $alteristPathDir.'/sets/'.$id.'/'.$id.'-combined-mini.png';
        $savePathLo   = $alteristPathDir.'/sets/'.$id.'/'.$id.'-combined.png';
        $savePathHi   = $alteristPathDir.'/sets/'.$id.'/'.$id.'-combined@2x.png';
        $bgPath       = DIR_THEME_IMAGES.'/generation/social-poster/backgrounds/transparent.png';
        $bgPath       = ImageManagerStatic::make( $bgPath );
        $bgPath->resize( 1000, 1000 );
        
        $compositeData = get_post_meta( $id, '_bto_data', true );
        $sleeves       = [];
        foreach( $compositeData as $singleSleeve ) {
            $sleeveId = $singleSleeve['default_id'];
            if( $sleeveId == '' ) break;
            $sleeves[] = $sleeveId;
        }
        
        $set = '';
        if( has_term( 31, 'set_type', $id ) ) {
            $set = '2-snap';
        } // 3 Basic Land
        else {
            if( has_term( 26, 'set_type', $id ) ) {
                $set = '3-pan';
            } // 4 Seasons
            else {
                if( has_term( 27, 'set_type', $id ) ) {
                    $set = '4-sq';
                } // 5 Panoramic Land
                else {
                    if( has_term( 28, 'set_type', $id ) ) {
                        $set = '5-pan';
                    } // 6 Commander
                    else {
                        if( has_term( 29, 'set_type', $id ) ) {
                            $set = '6-com';
                        }
                    }
                }
            }
        }
        if( $set == '' ) return false;
        $i = 1;
        foreach( $sleeves as $alter ) {
            $cardPath  = MC_Url::urlToPath( MC_Mtg_Printing_Functions::img_png( MC_Alter_Functions::printing( $alter ) ) );
            $alterPath = MC_Url::urlToPath( get_post_meta( $alter, 'as_hi_res_alter_png', true ) );
            
            $sleeve = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/270.png';
            $sleeve = ImageManagerStatic::make( $sleeve );
            $card   = ImageManagerStatic::make( $cardPath );
            $alter  = ImageManagerStatic::make( $alterPath );
            
            switch( $set ) {
                /** 2 Snapbolt */
                case '2-snap' :
                    $sleeve = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/300.png';
                    $sleeve = ImageManagerStatic::make( $sleeve );
                    $card->resize( 290, 404 );
                    $alter->resize( 290, 404 );
                    
                    $sleeve->insert( $card, 'top-left', 5, 7 );
                    $sleeve->insert( $alter, 'top-left', 5, 7 );
                    break;
                /** 3 Panoramic */
                case '5-bv':
                case '3-pan' :
                    // Resize Card and Alter
                    
                    $card->resize( 262, 364 );
                    $alter->resize( 262, 364 );
                    $sleeve->insert( $card, 'top-left', 4, 7 );
                    $sleeve->insert( $alter, 'top-left', 4, 7 );
                    break;
                case '4-sq' :
                    // Resize Card and Alter
                    
                    $card->resize( 262, 364 );
                    $alter->resize( 262, 364 );
                    
                    $sleeve->insert( $card, 'top-left', 4, 7 );
                    $sleeve->insert( $alter, 'top-left', 4, 7 );
                    $rotate = -9;
                    if( $i == 1 || $i == 3 ) {
                        $rotate = 9;
                    }
                    $sleeve->rotate( $rotate );
                    break;
                /** 5 Non-Panoramic 'Basic View' */
                case '5-pan' :
                    $sleeve = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/190.png';
                    $sleeve = ImageManagerStatic::make( $sleeve );
                    // Resize Card and Alter
                    $card->resize( 184, 256 );
                    $alter->resize( 184, 256 );
                    
                    $sleeve->insert( $card, 'top-left', 3, 5 );
                    $sleeve->insert( $alter, 'top-left', 3, 5 );
                    break;
                case '6-com' :
                    // Resize Card and Alter
                    if( $i == 1 ) {
                        $sleeve = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/300.png';
                        $sleeve = ImageManagerStatic::make( $sleeve );
                        
                        $card->resize( 290, 404 );
                        $alter->resize( 290, 404 );
                        
                        $sleeve->insert( $card, 'top-left', 5, 7 );
                        $sleeve->insert( $alter, 'top-left', 5, 7 );
                    } else {
                        $card->resize( 262, 364 );
                        $alter->resize( 262, 364 );
                        
                        $sleeve->insert( $card, 'top-left', 4, 7 );
                        $sleeve->insert( $alter, 'top-left', 4, 7 );
                    }
                    break;
            }
            
            /** 2 Panorama */
            if( $set == '2-snap' ) {
                $offsetX = 0;
                $offsetY = 0;
                switch( $i ) {
                    case 1 :
                        $offsetX = 160;
                        $offsetY = 298;
                        break;
                    case 2 :
                        $offsetX = 560;
                        $offsetY = 298;
                        break;
                }
                $bgPath->insert( $sleeve, 'top-left', $offsetX, $offsetY );
                $bgPath->save( $savePath );
            } /** 3 Panorama */
            else {
                if( $set == '3-pan' ) {
                    $offset = 0;
                    switch( $i ) {
                        case 1 :
                            $offset = 80;
                            break;
                        case 2 :
                            $offset = 365;
                            break;
                        case 3 :
                            $offset = 650;
                            break;
                    }
                    $bgPath->insert( $sleeve, 'top-left', $offset, 312 );
                    $bgPath->save( $savePath );
                } /** 4 Seasons */
                else {
                    if( $set == '4-sq' ) {
                        $offsetX = 0;
                        $offsetY = 0;
                        switch( $i ) {
                            case 1 :
                                $offsetX = 140;
                                $offsetY = 60;
                                break;
                            case 2 :
                                $offsetX = 520;
                                $offsetY = 60;
                                break;
                            case 3 :
                                $offsetX = 140;
                                $offsetY = 500;
                                break;
                            case 4 :
                                $offsetX = 520;
                                $offsetY = 500;
                                break;
                        }
                        $bgPath->insert( $sleeve, 'top-left', $offsetX, $offsetY );
                        $bgPath->save( $savePath );
                    } /** 5 Panoramic */
                    else {
                        if( $set == '5-pan' ) {
                            $offsetX = 0;
                            $offsetY = 372;
                            switch( $i ) {
                                case 1 :
                                    $offsetX = 20;
                                    break;
                                case 2 :
                                    $offsetX = 214;
                                    break;
                                case 3 :
                                    $offsetX = 408;
                                    break;
                                case 4 :
                                    $offsetX = 602;
                                    break;
                                case 5 :
                                    $offsetX = 796;
                                    break;
                            }
                            $bgPath->insert( $sleeve, 'top-left', $offsetX, $offsetY );
                            $bgPath->save( $savePath );
                        } /** 5 Non-panoramic 'Basic View' */
                        else {
                            if( $set == '5-bv' ) {
                                $offsetX = 0;
                                $offsetY = 0;
                                switch( $i ) {
                                    case 1 :
                                        $offsetX = 215;
                                        $offsetY = 110;
                                        break;
                                    case 2 :
                                        $offsetX = 500;
                                        $offsetY = 110;
                                        break;
                                    case 3 :
                                        $offsetX = 80;
                                        $offsetY = 500;
                                        break;
                                    case 4 :
                                        $offsetX = 365;
                                        $offsetY = 500;
                                        break;
                                    case 5 :
                                        $offsetX = 650;
                                        $offsetY = 500;
                                        break;
                                }
                                $bgPath->insert( $sleeve, 'top-left', $offsetX, $offsetY );
                                $bgPath->save( $savePath );
                            } /** 6 Commander */
                            else {
                                if( $set == '6-com' ) {
                                    $offsetX = 0;
                                    $offsetY = 0;
                                    switch( $i ) {
                                        case 1 :
                                            $offsetX = 350;
                                            $offsetY = 500;
                                            break;
                                        case 2 :
                                            $offsetX = 80;
                                            $offsetY = 70;
                                            break;
                                        case 3 :
                                            $offsetX = 365;
                                            $offsetY = 70;
                                            break;
                                        case 4 :
                                            $offsetX = 650;
                                            $offsetY = 70;
                                            break;
                                        case 5 :
                                            $offsetX = 50;
                                            $offsetY = 520;
                                            break;
                                        case 6 :
                                            $offsetX = 680;
                                            $offsetY = 520;
                                            break;
                                    }
                                    $bgPath->insert( $sleeve, 'top-left', $offsetX, $offsetY );
                                    $bgPath->save( $savePath );
                                }
                            }
                        }
                    }
                }
            }
            $i++;
        }
        $bgPath->save( $savePath );
        MC_Images::compress( $savePath );
        update_post_meta( $id, 'as_combined_png', MC_Url::pathToUrl( $savePath ) );
        
        $bgPath->resize( 600, 600 );
        $bgPath->save( $savePathHi );
        MC_Images::compress( $savePathHi );
        update_post_meta( $id, 'as_hi_res_combined_png', MC_Url::pathToUrl( $savePathHi ) );
        
        $bgPath->resize( 300, 300 );
        $bgPath->save( $savePathLo );
        MC_Images::compress( $savePathLo );
        update_post_meta( $id, 'as_lo_res_combined_png', MC_Url::pathToUrl( $savePathLo ) );
        
        $bgPath->resize( 200, 200 );
        $bgPath->save( $savePathMini );
        MC_Images::compress( $savePathMini );
        update_post_meta( $id, 'as_mini_combined_png', MC_Url::pathToUrl( $savePathMini ) );
        
        // Set Background
        $backgrounds    = [ 'art', 'dreamcatcher', 'featherlight', 'fire', 'forest', 'map', 'monastery', 'mountain', 'mystic', 'nightsky', 'rainforest', 'sea', 'ship', 'sunrise', 'water' ];
        $rand           = array_rand( $backgrounds, 1 );
        $savePathSocial = $alteristPathDir.'/sets/'.$id.'/'.$id.'-social.jpg';
        $bgImage        = DIR_THEME_IMAGES.'/generation/social-poster/backgrounds/'.$backgrounds[ $rand ].'.png';
        $bgPath         = ImageManagerStatic::make( $savePath );
        $bgImage        = ImageManagerStatic::make( $bgImage );
        $bgImage->resize( 1000, 1000 );
        $bgImage->insert( $bgPath, 'top-left', 0, 0 );
        $bgImage->save( $savePathSocial );
        MC_Images::compress( $savePathSocial );
        update_post_meta( $id, 'as_social_square', MC_Url::pathToUrl( $savePathSocial ) );
        return true;
    }
    
}