<?php

namespace Mythic_Core\Functions;

use MC_Alter_Functions;
use MC_Mtg_Printing;
use MC_WP;

/**
 * Class MC_Licensing_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Licensing_Functions {
    
    public static $product_rights_sharing_table_name = 'mc_products_rights_sharing';
    public static $status_labels = [
        1 => 'Not accepted',
        2 => 'Accepted',
        3 => 'Declined by artist',
        4 => 'Declined by publisher',
        5 => 'Cancelled by artist',
        6 => 'Cancelled by publisher',
    ];
    
    /**
     * Returns all shared products for publisher
     *
     * @param int $publisher_id
     * @param int $limit
     * @param int $offset
     *
     * @return array|object|null
     */
    public static function getAllSharedProductIdsForPublisher( $publisher_id = 0, $limit = 0, $offset = 0 ) {
        $publisher_id = !empty( $publisher_id ) ? $publisher_id : get_current_user_id();
        if( empty( $publisher_id ) ) return [];
        
        $products = static::getProductRightsSharing( 2, 0, $publisher_id, 0, $limit, $offset );
        if( empty( $products ) ) return [];
        foreach( $products as $key => $product ) {
            $products[ $key ] = $product['product_id'];
        }
        return $products;
    }
    
    /**
     * Returns all shared products for publisher
     *
     * @param int $publisher_id
     * @param int $limit
     * @param int $offset
     *
     * @return array|object|null
     */
    public static function getAllSharedProductIds() {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
    
        $query        = "SELECT * FROM $table_name";
    
        $products = $wpdb->get_results( $query, ARRAY_A );
        
        if( empty( $products ) ) return [];
        foreach( $products as $key => $product ) {
            $products[ $key ] = $product['product_id'];
        }
        return $products;
    }
    
    /**
     * @param int $product_id
     * @param int $user_id
     *
     * @return bool
     */
    public static function userPublisherOfProduct( $product_id = 0, $user_id = 0 ) {
        global $current_user_id;
        if( empty( $product_id ) ) {
            $object = get_queried_object();
            if( empty( $object ) ) return false;
            $product_id = $object->ID;
        }
        
        if( empty( $user_id ) ) $user_id = $current_user_id;
        $productsByPublisher = self::getAllSharedProductIdsForPublisher( $user_id );
        if( empty( $productsByPublisher ) ) return false;
        return in_array( $product_id, $productsByPublisher );
    }
    
    /**
     * Returns all product rights sharing
     *
     * @param int $status
     * @param int $artist_id
     * @param int $publisher_id
     * @param int $product_id
     * @param int $limit
     * @param int $offset
     *
     * @return array|object|null
     */
    public static function getProductRightsSharing( $status = 0, $artist_id = 0, $publisher_id = 0, $product_id = 0,
                                                    $limit = 0, $offset = 0 ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        
        $query        = "SELECT * FROM $table_name";
        $where_or_and = 'WHERE';
        
        if( !empty( $status ) ) {
            $query        .= " $where_or_and status = $status";
            $where_or_and = 'AND';
        }
        if( !empty( $artist_id ) ) {
            $query        .= " $where_or_and artist_id = $artist_id";
            $where_or_and = 'AND';
        }
        if( !empty( $publisher_id ) ) {
            $query        .= " $where_or_and publisher_id = $publisher_id";
            $where_or_and = 'AND';
        }
        if( !empty( $product_id ) ) {
            $query .= " $where_or_and product_id = $product_id";
        }
        
        $query .= " ORDER BY id DESC";
        if( !empty( $limit ) ) {
            $query .= " LIMIT $offset, $limit";
        }
        
        return $wpdb->get_results( $query, ARRAY_A );
    }
    
    /**
     * Returns all product rights sharing
     *
     * @param int $status
     * @param int $artist_id
     * @param int $publisher_id
     * @param int $product_id
     * @param int $limit
     * @param int $offset
     *
     * @return array|object|null
     */
    public static function getLicensingByPublisher( $status = 0, $publisher_id = 0, $product_id = 0,
                                                    $limit = 0, $offset = 0 ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        
        $query        = "SELECT * FROM $table_name";
        $where_or_and = 'WHERE';
        
        if( !empty( $status ) ) {
            $query        .= " $where_or_and status = $status";
            $where_or_and = 'AND';
        }
        if( !empty( $publisher_id ) ) {
            $query        .= " $where_or_and publisher_id = $publisher_id";
            $where_or_and = 'AND';
        }
        if( !empty( $product_id ) ) {
            $query .= " $where_or_and product_id = $product_id";
        }
        
        $query .= " ORDER BY id DESC";
        if( !empty( $limit ) ) {
            $query .= " LIMIT $offset, $limit";
        }
        
        return $wpdb->get_results( $query, ARRAY_A );
    }
    
    /**
     * Checks if publisher has accepted share for product
     *
     * @param     $product_id
     * @param int $publisher_id
     *
     * @return bool
     */
    public static function checkIfPublisherHasRights( $product_id, $publisher_id = 0 ) {
        $publisher_id = !empty( $publisher_id ) ? $publisher_id : get_current_user_id();
        if( empty( $publisher_id ) ) return false;
        
        $share_status = static::getShareStatusForPublisher( $publisher_id, $product_id );
        
        return !empty( $share_status ) && $share_status == 1;
    }
    
    /**
     * Returns share status for publisher if it's exist
     *
     * @param $publisher_id
     * @param $product_id
     *
     * @return string|null
     */
    public static function getShareStatusForPublisher( $publisher_id, $product_id ) {
        global $wpdb;
        $table_name   = static::$product_rights_sharing_table_name;
        $publisher_id = intval( $publisher_id );
        
        $query = "SELECT status FROM $table_name WHERE publisher_id = $publisher_id AND product_id = $product_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Registers new product rights shares
     *
     * @param $productRightsShareData
     *
     * @return array|bool
     */
    public static function registerNewProductRightsShares( $productRightsShareData ) {
        $result = [ 'status' => 0, 'message' => 'Something went wrong' ];
        if(
            empty( $productRightsShareData['publisherId'] ) ||
            empty( $productRightsShareData['productIds'] )
        ) {
            return $result;
        }
        
        $product_ids   = explode( ',', $productRightsShareData['productIds'] );
        $error_message = '';
        
        foreach( $product_ids as $product_id ) {
            if( empty( $product_id ) ) continue;
            
            $product = wc_get_product( $product_id );
            
            if( empty( $product ) ) {
                $error_message .= $product_id.' is wrong product ID. ';
                continue;
            }
            
            $author_id = MC_WP::authorId( $product_id );
            if( !MC_User_Functions::isAdmin() && ( empty( $author_id ) || $author_id != $productRightsShareData['artistId'] ) ) {
                $error_message .= 'You don\'t have permissions for share '.$product_id.' product. ';
                continue;
            }
            
            if( !empty( static::checkIfShareExistsByProduct( $product_id ) ) ) {
                $error_message .= 'Share for '.$product_id.' product already exists. ';
                continue;
            }
            
            if( empty( static::registerNewProductRightsShareSingle(
                $author_id,
                $productRightsShareData['publisherId'],
                trim( $product_id )
            ) ) ) {
                $error_message .= 'Something went wrong while saving data';
                continue;
            }
        }
        
        if( empty( $error_message ) ) {
            $result = [ 'status' => 1, 'message' => 'Product rights share was added!' ];
        } else {
            $result['message'] = $error_message;
        }
        
        return $result;
    }
    
    /**
     * Check if share exists by Product ID only
     *
     * @param $product_id
     *
     * @return string|null
     */
    public static function checkIfShareExistsByProduct( $product_id ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        $product_id = intval( $product_id );
        
        $query = "SELECT id FROM $table_name WHERE product_id = $product_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Registers new product rights single share
     *
     * @param $artist_id
     * @param $publisher_id
     * @param $product_id
     *
     * @return false|int
     */
    public static function registerNewProductRightsShareSingle( $artist_id, $publisher_id, $product_id ) {
        global $wpdb;
        $table_name  = static::$product_rights_sharing_table_name;
        $data        = [
            'artist_id'    => $artist_id,
            'publisher_id' => $publisher_id,
            'product_id'   => $product_id,
            'status'       => 1,
        ];
        $data_format = [ '%d', '%d', '%d', '%d' ];
        
        return $wpdb->insert( $table_name, $data, $data_format );
    }
    
    public static function checkAndUpdateShareStatus( $share_id, $status, $user_type ) {
        if( empty( $share_id ) || empty( $status ) || empty( $user_type ) ) return false;
        
        $user_id = $user_type == 'artist' ? static::getArtistIdByShareId( $share_id ) : static::getPublisherIdByShareId( $share_id );
        
        if( !MC_User_Functions::isAdmin() && get_current_user_id() != $user_id ) return false;
        
        return static::updateProductRightsShareStatus( $share_id, $status );
    }
    
    /**
     * Returns Artist ID by Share ID
     *
     * @param $share_id
     *
     * @return string|null
     */
    public static function getArtistIdByShareId( $share_id ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        $share_id   = intval( $share_id );
        
        $query = "SELECT artist_id FROM $table_name WHERE id = $share_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Returns Publisher ID by Share ID
     *
     * @param $share_id
     *
     * @return string|null
     */
    public static function getPublisherIdByShareId( $share_id ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        $share_id   = intval( $share_id );
        
        $query = "SELECT publisher_id FROM $table_name WHERE id = $share_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Update product rights share status
     * Statuses list:
     * 1 - not accepted
     * 2 - accepted
     * 3 - declined by artist
     * 4 - declined by publisher
     * 5 - cancelled by artist
     * 6 - cancelled by publisher
     *
     * @param $share_id
     * @param $status
     *
     * @return bool|false|int
     */
    public static function updateProductRightsShareStatus( $share_id, $status ) {
        if( empty( $share_id ) || empty( $status ) ) return false;
        
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        $data       = [ 'status' => $status ];
        $where      = [ 'id' => $share_id ];
        
        return $wpdb->update( $table_name, $data, $where, [ '%d' ], [ '%d' ] );
    }
    
    /**
     * Returns status label
     *
     * @param $status
     *
     * @return mixed|string
     */
    public static function prepareStatusLabel( $status ) {
        $status = intval( $status );
        
        return !empty( static::$status_labels[ $status ] ) ? static::$status_labels[ $status ] : '';
    }
    
    /**
     * Generates new status actions for artist
     *
     * @param $share_id
     * @param $current_status
     *
     * @return string
     */
    public static function generateStatusActionForArtist( $share_id, $current_status ) {
        $result = '';
        
        switch( $current_status ) {
            case 1:
                $data = [
                    [
                        'new_status' => 3,
                        'label'      => 'Decline',
                    ],
                ];
                break;
            case 2:
                $data = [
                    [
                        'new_status' => 5,
                        'label'      => 'Cancel',
                    ],
                ];
                break;
        }
        
        if( empty( $data ) ) return $result;
        foreach( $data as $data_single ) {
            $result .= static::generateProdShareActionHtml( $share_id, $data_single );
        }
        
        return $result;
    }
    
    /**
     * Generates product share action HTML
     *
     * @param $share_id
     * @param $data_single
     *
     * @return string
     */
    public static function generateProdShareActionHtml( $share_id, $data_single ) {
        return "<a href='#' class='mc-prod-share-action' data-prod-share-id='".$share_id.
               "' data-prod-share-new-status='".$data_single['new_status']."'>".$data_single['label']."</a>";
    }
    
    /**
     * Generates new status actions for publisher
     *
     * @param $share_id
     * @param $current_status
     *
     * @return string
     */
    public static function generateStatusActionForPublisher( $share_id, $current_status ) {
        $result = '';
        
        switch( $current_status ) {
            case 1:
                $data = [
                    [
                        'new_status' => 2,
                        'label'      => 'Accept',
                    ],
                    [
                        'new_status' => 4,
                        'label'      => 'Decline',
                    ],
                ];
                break;
            case 2:
                $data = [
                    [
                        'new_status' => 6,
                        'label'      => 'Cancel',
                    ],
                ];
                break;
        }
        
        if( empty( $data ) ) return $result;
        foreach( $data as $data_single ) {
            $result .= static::generateProdShareActionHtml( $share_id, $data_single );
        }
        
        return $result;
    }
    
    /**
     * Returns all product rights sharing count
     *
     * @param int $status
     * @param int $artist_id
     * @param int $publisher_id
     * @param int $product_id
     *
     * @return string|null
     */
    public static function getProductRightsSharingCount( $status = 0, $artist_id = 0, $publisher_id = 0,
                                                         $product_id = 0 ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        
        $query        = "SELECT COUNT(*) FROM $table_name";
        $where_or_and = 'WHERE';
        
        if( !empty( $status ) ) {
            $query        .= " $where_or_and status = $status";
            $where_or_and = 'AND';
        }
        if( !empty( $artist_id ) ) {
            $query        .= " $where_or_and artist_id = $artist_id";
            $where_or_and = 'AND';
        }
        if( !empty( $publisher_id ) ) {
            $query        .= " $where_or_and publisher_id = $publisher_id";
            $where_or_and = 'AND';
        }
        if( !empty( $product_id ) ) {
            $query .= " $where_or_and product_id = $product_id";
        }
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Check if share exists by Product ID only
     *
     * @param $product_id
     *
     * @return string|null
     */
    public static function getPublisherWithSharingRights( $product_id ) {
        global $wpdb;
        $table_name = static::$product_rights_sharing_table_name;
        $product_id = intval( $product_id );
        
        $query = "SELECT publisher_id FROM $table_name WHERE product_id = $product_id AND status = 1";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Check if share exists by Artist ID, Publisher ID and Product ID
     *
     * @param $artist_id
     * @param $publisher_id
     * @param $product_id
     *
     * @return string|null
     */
    public static function checkIfShareExistsFull( $artist_id, $publisher_id, $product_id ) {
        global $wpdb;
        $table_name   = static::$product_rights_sharing_table_name;
        $artist_id    = intval( $artist_id );
        $publisher_id = intval( $publisher_id );
        $product_id   = intval( $product_id );
        
        $query = "SELECT id FROM $table_name WHERE artist_id = $artist_id AND publisher_id = $publisher_id AND product_id = $product_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Returns fields for registration new product share form
     *
     * @return array
     */
    public static function getNewProdShareFields() {
        return [
            [
                'label'    => 'Product id/ids (comma separated)',
                'name'     => 'productIds',
                'type'     => 'text',
                'id_part'  => 'prod-share-new-product-ids',
                'required' => 1,
            ],
            [
                'label'    => 'Publisher',
                'name'     => 'publisher',
                'type'     => 'affiliateAutocomplete',
                'id_part'  => 'prod-share-new-publisher',
                'required' => 1,
            ],
            [
                'label'    => '<small>I consent to providing all publishing rights to the intent Publisher</small>',
                'name'     => 'legalCheckbox',
                'type'     => 'checkbox',
                'id_part'  => 'prod-share-new-legal',
                'required' => 1,
            ],
        ];
    }
    
    /**
     * Creates additional product rights sharing table
     */
    public static function addProductsRightsSharingTable() {
        global $wpdb;
        $table_name = 'mc_products_rights_sharing';
        
        $create_table_query = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
              `artist_id` BIGINT(20) UNSIGNED NOT NULL,
              `publisher_id` BIGINT(20) UNSIGNED NOT NULL,
              `product_id` BIGINT(20) UNSIGNED NOT NULL,
              `status` TINYINT (1) DEFAULT NULL
            ) {$wpdb -> get_charset_collate()};";
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        
        dbDelta( $create_table_query );
    }
    
    /**
     * TODO: move this function after merging in mythic-core
     *
     * @param $product_id
     *
     * @return string
     */
    public static function getProductLinkWithImage( $product_id ) {
        if( empty( $idPrinting = MC_Mtg_Printing_Functions::idForSelection( $product_id ) ) ) return '';
        $printing = new MC_Mtg_Printing( $idPrinting );
        
        if( empty( $name_card = $printing->name ) ) return '';
        
        if( empty( $image = MC_Alter_Functions::getCombinedImage( $product_id, $idPrinting ) ) ) return '';
        
        $url = get_the_permalink( $product_id ).'?printing_id='.$idPrinting.'&card_name='.urlencode( $name_card );
        
        return '<a href="'.$url.'"><img class="card-corners card-display" src="'.$image.'" alt=""></a>';
    }
    
}