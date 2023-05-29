<?php

namespace Mythic\Functions\Store\Product;

use Intervention\Image\ImageManagerStatic;
use Mythic\Abstracts\MC2_DB_Table;
use Mythic\Functions\Products\MC2_Alter_Functions;
use Mythic\Helpers\MC2_Images;
use Mythic\Helpers\MC2_Url;
use Mythic\Helpers\MC2_Vars;

class MC2_Collection_Functions extends MC2_DB_Table {

    protected static $table_name = 'collections';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
                  `design_id` int(11) unsigned NOT NULL,
                  `collection_id` int(11) unsigned NOT NULL,
                  KEY `collection_id` (`collection_id`),
                  KEY `design_id` (`design_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * @return string[]
     */
    public function meta_tables() : array {
        return [
            'collection_images',
        ];
    }

    /**
     * @return string[]
     */
    public function create_meta_table_queries() : array {
        return [
            'collection_images' =>
                "CREATE TABLE `wp_mc_collection_images` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `collection_id` int(11) unsigned NOT NULL,
                  `image_size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file` longtext COLLATE utf8mb4_unicode_ci,
                  `path` longtext COLLATE utf8mb4_unicode_ci,
                  `url` longtext COLLATE utf8mb4_unicode_ci,
                  PRIMARY KEY (`id`),
                  KEY `image_size` (`image_size`),
                  KEY `file_type` (`file_type`),
                  KEY `collection_id` (`collection_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        ];
    }
    
    
    /**
     * @param int $idCollection
     *
     * @return array
     */
    public static function designs( $idCollection = 0 ) {
        if( empty( $idCollection ) ) return [];
        $singles = self::singles( $idCollection );
        foreach( $singles as $key => $single ) {
            $id   = $single['default_id'];
            $type = get_post_type( $id );
            switch( $type ) {
                case 'design' :
                    $singles[ $key ] = $id;
                    break;
                case ( 'product' && MC2_Product_Functions::isAlter( $id ) ) :
                    $idAlter  = $id;
                    $idDesign = MC2_Alter_Functions::design( $idAlter );
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
        $data = MC2_WP::meta( '_bto_data', $idCollection );
        
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
        if( MC2_User_Functions::isAdmin() ) unset( $args['author'] );
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
            'title'                    => MC2_Vars::generate( 10 ),
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
            $printing_id = MC2_Alter_Functions::printing( $alter );
            $printing    = new MC2_Printing( $printing_id );
            $cardPath    = MC2_Url::urlToPath( $printing->getImgPng() );
            $alterPath   = MC2_Url::urlToPath( get_post_meta( $alter, 'mc_hi_res_alter_png', true ) );
            
            $sleeve = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/270.png';
            $sleeve = ImageManagerStatic::make( $sleeve );
            $card   = ImageManagerStatic::make( $cardPath );
            $alter  = ImageManagerStatic::make( $alterPath );
            
            switch( $set ) {
                /** 2 Snapbolt */ case '2-snap' :
                $sleeve = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/300.png';
                $sleeve = ImageManagerStatic::make( $sleeve );
                $card->resize( 290, 404 );
                $alter->resize( 290, 404 );
                
                $sleeve->insert( $card, 'top-left', 5, 7 );
                $sleeve->insert( $alter, 'top-left', 5, 7 );
                break;
                /** 3 Panoramic */ case '5-bv':
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
                /** 5 Non-Panoramic 'Basic View' */ case '5-pan' :
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
            } /** 3 Panorama */ else {
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
                } /** 4 Seasons */ else {
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
                    } /** 5 Panoramic */ else {
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
                        } /** 5 Non-panoramic 'Basic View' */ else {
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
                            } /** 6 Commander */ else {
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
        MC2_Images::compress( $savePath );
        update_post_meta( $id, 'mc_combined_png', MC2_Url::pathToUrl( $savePath ) );
        
        $bgPath->resize( 600, 600 );
        $bgPath->save( $savePathHi );
        MC2_Images::compress( $savePathHi );
        update_post_meta( $id, 'mc_hi_res_combined_png', MC2_Url::pathToUrl( $savePathHi ) );
        
        $bgPath->resize( 300, 300 );
        $bgPath->save( $savePathLo );
        MC2_Images::compress( $savePathLo );
        update_post_meta( $id, 'mc_lo_res_combined_png', MC2_Url::pathToUrl( $savePathLo ) );
        
        $bgPath->resize( 200, 200 );
        $bgPath->save( $savePathMini );
        MC2_Images::compress( $savePathMini );
        update_post_meta( $id, 'mc_mini_combined_png', MC2_Url::pathToUrl( $savePathMini ) );
        
        // Set Background
        $backgrounds    = [
            'art',
            'dreamcatcher',
            'featherlight',
            'fire',
            'forest',
            'map',
            'monastery',
            'mountain',
            'mystic',
            'nightsky',
            'rainforest',
            'sea',
            'ship',
            'sunrise',
            'water',
        ];
        $rand           = array_rand( $backgrounds, 1 );
        $savePathSocial = $alteristPathDir.'/sets/'.$id.'/'.$id.'-social.jpg';
        $bgImage        = DIR_THEME_IMAGES.'/generation/social-poster/backgrounds/'.$backgrounds[ $rand ].'.png';
        $bgPath         = ImageManagerStatic::make( $savePath );
        $bgImage        = ImageManagerStatic::make( $bgImage );
        $bgImage->resize( 1000, 1000 );
        $bgImage->insert( $bgPath, 'top-left', 0, 0 );
        $bgImage->save( $savePathSocial );
        MC2_Images::compress( $savePathSocial );
        update_post_meta( $id, 'mc_social_square', MC2_Url::pathToUrl( $savePathSocial ) );
        
        return true;
    }

}