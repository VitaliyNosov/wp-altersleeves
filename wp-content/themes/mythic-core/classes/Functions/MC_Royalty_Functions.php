<?php

namespace Mythic_Core\Functions;

use MC_Licensing_Functions;
use MC_Vars;
use MC_Woo_Order_Functions;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\System\MC_WP;
use ReflectionClass;
use WC_Order_Query;


/**
 * Old functionality (still active)
 */

/**
 * Class MC_Royalty_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Royalty_Functions {
    
    const TABLE_NAME        = 'as_royalties';
    const DB_ID             = 'id';
    const DB_ALTERIST_ID    = 'alterist_id';
    const DB_ORDER_ID       = 'order_id';
    const DB_PRODUCT_ID     = 'product_id';
    const DB_QUANTITY       = 'quantity';
    const DB_DATE           = 'date';
    const DB_TYPE           = 'type';
    const DB_TAG            = 'tag';
    const DB_CLEARED        = 'cleared';
    const DB_VALUE          = 'value';
    public $quantity;
    public $date;
    public $type;
    public $cleared;
    public $value;
    protected $id;
    protected $alteristId;
    protected $orderId;
    protected $productId;
    
    /**
     * @return array
     */
    public static function getAll() : array {
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $query      = "SELECT * FROM $table_name";
        $results    = $wpdb->get_results( $query );
        if( $results == null ) return [];
        
        return $results;
    }
    
    /**
     * @param string $order
     *
     * @return array|object
     */
    public static function getAllWithValueByDate( string $order = 'DESC' ) {
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $query      = "SELECT * FROM $table_name WHERE value > 0 ORDER BY date $order";
        $results    = $wpdb->get_results( $query );
        if( $results == null ) return [];
        
        return $results;
    }
    
    /**
     * @param string $query
     * @param bool   $init
     *
     * @return array|mixed
     */
    public static function getFromQuery( $query = '', $init = false ) : array {
        if( $query == '' ) return [];
        global $wpdb;
        
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $start      = "SELECT * FROM $table_name ";
        if( $init ) $start = "";
        $query   = $start.$query;
        $results = $wpdb->get_results( $query );
        if( $results == null ) return [];
        
        return $results;
    }
    
    /**
     * @param int  $id
     * @param bool $clearedOnly
     *
     * @return array|bool|object|null
     */
    public static function getByProductId( $id = 0, $clearedOnly = false, $sales = true ) {
        if( $id == 0 ) return false;
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        
        /** Get all from table */
        $query = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_PRODUCT_ID.'="'.$id.'"';
        if( $clearedOnly ) $query .= ' AND cleared = 1';
        
        $results = $wpdb->get_results( $query );
        if( !$sales ) return $results;
        foreach( $results as $key => $result ) {
            $order_id = $result->order_id;
            $order    = wc_get_order( $order_id );
            if( empty( $order ) ) continue;
            $customer_id = $order->get_user_id();
            if( $customer_id == $order->get_user_id() || $customer_id == 1 || $customer_id == 2 ) continue;
            unset( $results[ $key ] );
        }
        return $results;
    }
    
    public static function importRoyalties() {
        $query  = new WC_Order_Query( [
                                          'limit'        => 10,
                                          'order'        => 'DESC',
                                          'status'       => 'completed',
                                          'meta_key'     => 'mc_royalty_logged',
                                          'meta_compare' => 'NOT EXISTS',
                                      ] );
        $orders = $query->get_orders();
        foreach( $orders as $order ) {
            $order_id = $order->get_id();
            if( MC_Vars::stringContains( strtolower( get_the_title( $order_id ) ), 'refund' ) ) continue;
            $items = $order->get_items();
            foreach( $items as $item ) {
                $product_id = $item['product_id'];
                self::create( [
                                  self::DB_ORDER_ID   => $order_id,
                                  self::DB_PRODUCT_ID => $product_id,
                              ] );
            }
            update_post_meta( $order_id, 'mc_royalty_logged', 1 );
        }
        do_action( 'clear_royalties_from_cycle_orders' );
    }
    
    /**
     * @param array $info
     *
     * @return false|int
     */
    public static function create( $info = [] ) {
        if( empty( $info ) ) return 0;
        $tableColumns = self::getTableColumns();
        foreach( $info as $key => $infoItem ) {
            if( !in_array( $key, $tableColumns ) ) unset( $info[ $key ] );
        }
        if( !array_key_exists( self::DB_ORDER_ID, $info ) && !array_key_exists( self::DB_PRODUCT_ID, $info ) ) {
            return 0;
        }

        $product_id = $info[ self::DB_PRODUCT_ID ];
        if( in_array( $product_id, MC_Product_Functions::counterShieldIds() ) ) return 0;       

        $idCreator = MC_WP::authorId( $product_id );
        $share_id = MC_Licensing_Functions::checkIfShareExistsByProduct( $product_id );
        if ( $share_id ) {
           $idCreator =  MC_Licensing_Functions::getPublisherIdByShareId( $share_id );
        }

        $order_id  = $info[ self::DB_ORDER_ID ];
        $order     = wc_get_order( $order_id );

        if( user_can( $order->get_user_id(), 'administrator' ) ) return false;

        $idUser = 0;
        if( !empty( $order->get_user_id() ) ) $idUser = $order->get_user_id();
        // @todo remove later, just to stop Targa getting supplemental royalties
        if( $idUser == 9 ) return 0;
        
        if( $order->get_status() != 'completed' ) return false;
        
        $items    = $order->get_items();
        $quantity = 1;
        foreach( $items as $item ) {
            if( $item['product_id'] != $product_id ) continue;
            $quantity = $item['qty']; // product quantity
            break;
        }
        
        $info[ self::DB_ALTERIST_ID ] = $idCreator;
        $info[ self::DB_QUANTITY ]    = $quantity;
        $info[ self::DB_DATE ]        = $order->get_date_created()->date( 'Y-m-d H:i:s' );
        $info[ self::DB_CLEARED ]     = 0;
        
        // Value and Type
        $productValue           = self::getProductValue( $product_id, $order_id );
    
        $info[ self::DB_VALUE ] = is_array( $productValue ) ? $productValue['value'] : (float) 0.00;
        $info[ self::DB_TYPE ]  = $productValue['type'];
        $info['tag']            = json_encode( [] );
        
        $check = self::getByOrderIdAndProductId( $order_id, $product_id );
        if( !empty( $check ) ) {
            return false;
        }


        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $wpdb->insert( $table_name, $info );
        
        return $wpdb->insert_id;
    }
    
    /**
     * @return array
     */
    public static function getTableColumns() : array {
        $oClass         = new ReflectionClass( static::class );
        $constants      = $oClass->getConstants();
        $tableConstants = [];
        foreach( $constants as $key => $constant ) {
            if( substr( $key, 0, 3 ) == 'DB_' ) $tableConstants[] = $constant;
        }
        
        return $tableConstants;
    }
    
    /**
     * @param int $product_id
     * @param int $order_id
     *
     * @return array|int
     */
    public static function getProductValue( $product_id = 0, $order_id = 0 ) {
        $value = 0;
        if( $product_id == 0 && $order_id == 0 ) return $value;
        if( !is_string( get_post_status( $product_id ) ) ) return $value;
        
        $order     = wc_get_order( $order_id );
        $orderUser = $order->get_user_id();
        $idCreator = MC_WP::authorId( $product_id );
        if( $orderUser == $idCreator ) return 0;
        
        $type     = 'alter';
        $quantity = 1;
        foreach( $order->get_items() as $item ) {
            if( $item['product_id'] != $product_id ) continue;
            $quantity = $item['qty'];
            $price    = $item->get_total();
            if( MC_Woo_Order_Functions::order_product_collection_child( $item, $order ) ) {
                $type = 'child';
            } else {
                if( MC_Woo_Order_Functions::order_product_collection( $item ) ) {
                    $type = 'collection';
                } else {
                    if( ( has_term( 3183, 'commission_type', $product_id ) || has_term( 3185, 'commission_type', $product_id ) || has_term( 3184,
                                                                                                                                            'commission_type',
                                                                                                                                            $product_id ) ) && get_post_meta( $product_id,
                                                                                                                                                                              'mc_commission_claimed',
                                                                                                                                                                              true ) != 1 ) {
                        $type     = 'commission';
                        $quantity = 1;
                    }
                }
            }
            if( empty($price) && $type == 'alter' ) return 0;
            break;
        }
        
        $tag = [];
        if( $type == 'alter' && in_array( $idCreator, MC_Artist_Functions::getMtgArtists() ) ) {
            $value = 2.5;
        } else {
            $payment_method = $order->get_payment_method() ?? '';
            if( $type == 'alter' && $payment_method == 'wdc_woo_credits' ) {
                $value = 1.2;
            } else {
                if( $type == 'commission' ) {
                    if( has_term( 3183, 'commission_type', $product_id ) ) {
                        $value = 40;
                        $tag[] = [
                            'name'     => 'Kickstarter: 1 Digital 4 Prints',
                            'taxonomy' => 'commission_type',
                            'id'       => 3183,
                        ];
                    } else {
                        if( has_term( 3184, 'commission_type', $product_id ) ) {
                            $value = 60;
                            $tag[] = [
                                'name'     => 'Kickstarter: 1 Painted 4 Prints',
                                'taxonomy' => 'commission_type',
                                'id'       => 3184,
                            ];
                        } else {
                            if( has_term( 3186, 'commission_type', $product_id ) ) {
                                $value = 120;
                                $tag[] = [
                                    'name'     => 'Kickstarter: 4 Painted 4 Prints',
                                    'taxonomy' => 'commission_type',
                                    'id'       => 3186,
                                ];
                            }
                        }
                    }
                    update_post_meta( $product_id, 'mc_commission_claimed', 1 );
                } else {
                    if( $type == 'alter' ) {
                        $value = 1.2;
                    } else {
                        if( $type == 'collection' ) {
                            // Snapbolt
                            if( has_term( 31, 'set_type', $product_id ) ) {
                                $value = 0;
                                $tag[] = [
                                    'name'     => 'Snapbolt',
                                    'taxonomy' => 'set_type',
                                    'id'       => 31,
                                    'quantity' => 3,
                                ];
                            } // 3 Basic Land
                            else {
                                if( has_term( 26, 'set_type', $product_id ) ) {
                                    $value = 6;
                                    $tag[] = [
                                        'name'     => '3 Card Basic Land Set',
                                        'taxonomy' => 'set_type',
                                        'id'       => 26,
                                        'quantity' => 3,
                                    ];
                                } // 4 Seasons
                                else {
                                    if( has_term( 27, 'set_type', $product_id ) ) {
                                        $value = 9;
                                        $tag[] = [
                                            'name'     => '4 Seasons Set',
                                            'taxonomy' => 'set_type',
                                            'id'       => 27,
                                            'quantity' => 4,
                                        ];
                                    } // 5 Panoramic Land
                                    else {
                                        if( has_term( 28, 'set_type', $product_id ) ) {
                                            $value = 12;
                                            $tag[] = [
                                                'name'     => '5 Card Panoramic Land Set',
                                                'taxonomy' => 'set_type',
                                                'id'       => 28,
                                                'quantity' => 5,
                                            ];
                                        } // 6 Commander
                                        else {
                                            if( has_term( 29, 'set_type', $product_id ) ) {
                                                $value = 15;
                                                $tag[] = [
                                                    'name'     => '6 Card Commander Set',
                                                    'taxonomy' => 'set_type',
                                                    'id'       => 29,
                                                    'quantity' => 6,
                                                ];
                                            } else {
                                                $number = count( get_post_meta( $product_id, '_bto_data', true ) );
                                                if( $order->get_payment_method() == 'wdc_woo_credits' ) {
                                                    $value = 1.2;
                                                } else {
                                                    $value = 1.2;
                                                }
                                                $value = $number * $value;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $value = $value * $quantity;
    
        $order = wc_get_order($order_id);
        $coupons = $order->get_coupon_codes();
        foreach( $coupons as $coupon ) {
            if( strpos( strtolower($coupon), 'blackfriday') !== false ) {
                $value = $value / 2;
                break;
            }
        }
        
        return [
            'value' => $value,
            'type'  => $type,
        ];
    }
    
    /**
     * @param int $order_id
     * @param int $product_id
     *
     * @return mixed
     */
    public static function getByOrderIdAndProductId( $order_id = 0, $product_id = 0 ) {
        if( $order_id == 0 || $product_id == 0 ) return false;
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_PRODUCT_ID.'="'.$product_id.'" AND '.self::DB_ORDER_ID.' = "'.$order_id.'"';
        
        return $wpdb->get_results( $query );
    }
    
    public static function clearRoyalties() {
        $uncleared = self::getByClearedStatus( 0 );
        global $wpdb;
        $table_name   = $wpdb->prefix.self::TABLE_NAME;
        $dateClearing = time() - 2592000;
        
        foreach( $uncleared as $toClear ) {
            $toClearId = $toClear->id;
            $order_id  = $toClear->order_id;
            $order     = wc_get_order( $order_id );
            $date      = $order->get_date_created()->date( 'Y-m-d H:i:s' );
            $date      = strtotime( $date );
            if( $date > $dateClearing && !MC_User_Functions::isPublisher( $toClear->alterist_id ) ) continue;
            $sql = ' UPDATE '.$table_name.' SET ';
            $sql .= self::DB_CLEARED.' = 1 ';
            $sql .= "WHERE id ='$toClearId'";
            $wpdb->query( $sql );
        }
        do_action( 'update_royalties_from_orders' );
    }
    
    /**
     * @param int $id
     *
     * @return bool|object
     */
    public static function getByClearedStatus( $id = 1 ) {
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        /** Get all from table */
        $query   = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_CLEARED.'="'.$id.'"';
        $results = $wpdb->get_results( $query );
        
        return $results;
    }
    
    public static function updateRoyalties() {
        $royalties = self::getByClearedStatus( 0 );
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        foreach( $royalties as $royalty ) {
            $id         = $royalty->id;
            $order_id   = $royalty->order_id;
            $product_id = $royalty->product_id;
            $order      = wc_get_order( $order_id );
            $items      = $order->get_items();
            $confirm    = false;
            foreach( $items as $item_id => $item ) {
                $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
                if( $product_id == $product_id ) {
                    $confirm = true;
                    break;
                }
            }
            if( $confirm ) continue;
            $wpdb->delete( $table_name, [ 'id' => $id ] );
        }
        do_action( 'update_balances_from_royalties' );
    }
    
    public static function updateBalances() {
        $creators = get_users();
        foreach( $creators as $creator ) {
            $idCreator = $creator->ID;
            $royalties = self::getClearedByAlteristId( $idCreator );
            if( empty( $royalties ) ) continue;
            $earnings = 0;
            foreach( $royalties as $royalty ) {
                $earnings = $earnings + $royalty->value;
            }
            
            $deductions     = MC_User::meta( 'mc_deductions', $idCreator );
            $withdrawnTotal = MC_User::meta( 'mc_total_withdrawn', $idCreator );
            if( strlen( $withdrawnTotal ) == 0 || $withdrawnTotal == '' ) $withdrawnTotal = 0;
            $availableBalance = $earnings - $withdrawnTotal - $deductions;
            $earnings = number_format( round( $earnings, 2 ), 2 );
            update_user_meta( $idCreator, 'mc_lifetime_earnings', $earnings );
            $availableBalance = number_format( round( $availableBalance, 2 ), 2 );
            update_user_meta( $idCreator, 'mc_available_balance', $availableBalance );
            
            $royalties = self::getUnclearedByAlteristId( $idCreator );
            if( empty( $royalties ) ) continue;
            $pending = 0;
            foreach( $royalties as $royalty ) {
                $pending = $pending + $royalty->value;
            }
            $pending = number_format( round( $pending, 2 ), 2 );
            update_user_meta( $idCreator, 'mc_pending_balance', $pending );
        }
    }
    
    /**
     * @param int $id
     *
     * @return bool|object
     */
    public static function getClearedByAlteristId( $id = 0 ) {
        if( $id == 0 ) return false;
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        
        /** Get all from table */
        $query   = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_ALTERIST_ID.'="'.$id.'" AND '.self::DB_CLEARED.'= 1;';
        $results = $wpdb->get_results( $query );
        
        return $results;
    }
    
    /**
     * @param int $id
     *
     * @return bool|object
     */
    public static function getUnclearedByAlteristId( $id = 0 ) {
        if( $id == 0 ) return false;
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        
        /** Get all from table */
        $query   = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_ALTERIST_ID.'="'.$id.'" AND '.self::DB_CLEARED.'= 0;';
        $results = $wpdb->get_results( $query );
        
        return $results;
    }
    
    public static function updateToPublisher() {
        global $wpdb;
        $products = MC_Licensing_Functions::getProductRightsSharing( 2 );
        foreach( $products as $product ) {
            extract( $product );
            if( !empty( $publisher_id ) && !empty( $alterist_id ) && !empty( $product_id ) ) {
                $wpdb->update( 'wp_as_royalties', [ 'alterist_id' => $publisher_id ], [ 'product_id' => $product_id ] );
                $wpdb->update( 'mc_transactions', [ 'user_id' => $publisher_id ], [ 'type' => 'royalty', 'product_id' => $product_id ] );
            }
        }
    }


    /**
     * NEW functionality starts here
     */


    const NAME          = 'royalty';
    const READABLE_NAME = 'Royalty';


    /**
     * @return array
     */
    public static function getPostTypeSettings() : array 
    {
        return [
            'name'                => self::NAME,
            'label'               => self::READABLE_NAME.'ies',
            'label_singular'      => self::READABLE_NAME.'y',
            'supports'            => [ 'title' ],
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 10,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'menu_icon'           => 'dashicons-database-export',
            'can_export'          => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
        ];
    }


    /**
     * @param int $order_id
     *
     * @return void
     */
    public static function add_royalty( $order_id  ): void
    {
        $order          = wc_get_order( $order_id );
        $order_id       = $order->get_id();
        $order_status   = $order->get_status();

        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE '.self::DB_ORDER_ID.' = "'.$order_id.'"';
        $record_exist = $wpdb->get_results( $query );

        if( $order_status == 'completed' && !$record_exist )
        {
            self::CreateByOrderData( $order );
        }
    }


    /**
     * @param object $order
     *
     * @return bool|int
     */
    private static function createByOrderData( $order )  
    {
        $items = $order->get_items();

        foreach( $items as $item ) 
        {
            $order_id               = $order->get_id();
            $product_id             = $item->get_product_id();          
            $creator_id             = MC_WP::authorId( $product_id );
            $value                  = self::getValue( $item );

            if( !$value )
            {
                return false;
            }
            
            $info[ self::DB_ALTERIST_ID ]   = $creator_id;
            $info[ self::DB_ORDER_ID ]      = $order_id;
            $info[ self::DB_PRODUCT_ID ]    = $product_id;         
            $info[ self::DB_QUANTITY ]      = $item->get_quantity();
            $info[ self::DB_DATE ]          = $order->get_date_created()->date( 'Y-m-d H:i:s' );
            $info[ self::DB_TYPE ]          = self::getType( $item, $order );
            $info[ self::DB_TAG ]           = json_encode( [] );
            $info[ self::DB_CLEARED ]       = 0;
            $info[ self::DB_VALUE ]         = round( $value, 2 );
        }       

        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $wpdb->insert( $table_name, $info );
        
        return $wpdb->insert_id;
    }

    /**
     * @param object $item
     * @param object $product
     *
     * @return bool|float
     */
    private static function getValue( $item )
    {
        // We are working only with order item price, because product price can be changed before order will became completer
        $item_total     = $item->get_total();
        $item_quantity  = $item->get_quantity();
        $item_price     = $item_total / $item_quantity;

        // Get Royalty by logic. Any logic here
        $args = array(
            'post_type'    => self::NAME,
            'meta_key'     => 'user_type',
            'meta_value'   => 'author',
        );
        $query = new \WP_Query( $args );

        if ( !$query->have_posts() ) 
        {
            return false;            
        }
        $royalty_object     = $query->posts[0];
        $royalty_percentage = get_post_meta( $royalty_object->ID, 'royalty_percentage', true );
        $royalty            = $royalty_percentage / 100;
    
        return $item_price * $item_quantity * $royalty;
    }


    /**
     * @param object $item
     * @param object $order
     *
     * @return string
     */
    private static function getType( $item, $order ): string
    {
        $type = 'alter';
        if( MC_Woo_Order_Functions::order_product_collection_child( $item, $order ) ) 
        {
            $type = 'child';
        } else {
            if( MC_Woo_Order_Functions::order_product_collection( $item ) ) 
            {
                $type = 'collection';
            }
        }
        return $type;
    }

    
}