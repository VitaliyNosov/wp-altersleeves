<?php

namespace Mythic_Core\Functions;

use MC_Geo;
use Mythic_Core\Abstracts\MC_Post_Type_Functions;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Database;
use Mythic_Core\Utils\MC_Dates;
use Mythic_Core\Utils\MC_Vars;
use WC_Order;
use WC_Order_Query;
use WP_Query;

/**
 * Class MC_Woo_Order_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Woo_Order_Functions extends MC_Post_Type_Functions {
    
    /**
     * @param array|string $args
     *
     * @return array|object|null
     */
    public static function getOrdersPrintedForPeriod( $args = [] ) {
        global $wpdb;
        if( is_string( $args ) ) $args = [ 'start_date' => $args ];
        $site_id    = $args['site_id'] ?? 1;
        $start_date = $args['start_date'] ?? MC_Dates::currentDate();
        $end_date   = $args['end_date'] ?? $start_date;
        $period     = MC_Vars::getPeriodFromString( $args['period'] ?? '1 DAY', 'uppercase' );
        $office     = $args['office'] ?? '';
        $table      = MC_Database::multisiteTable( 'comments', $site_id );
        $query      = "SELECT * FROM $table WHERE comment_content LIKE '%Printed%' ";
        $query      .= "AND comment_date >= '$start_date' ";
        $query      .= "AND comment_date < ('$end_date' + INTERVAL $period) ";
        if( !empty( $office ) ) $query .= " AND comment_content LIKE '%$office%Office%'";
        return $wpdb->get_results( $query );
    }
    
    /**
     * @param array $args
     *
     * @return array
     */
    public static function getOrderCountPrintedForPeriod( array $args = [] ) : array {
        $orders  = self::getOrdersPrintedForPeriod( $args );
        $results = [ 'orders' => count( $orders ), 'sleeves' => 0 ];
        if( !is_array( $orders ) || empty( $orders ) ) return $results;
        $items = 0;
        foreach( $orders as $order ) {
            $order_id = $order->comment_post_ID ?? 0;
            if( empty( $order_id ) ) continue;
            $items += MC_Woo_Order_Functions::getItemCount( $order_id );
        }
        $results['sleeves'] = $items;
        return $results;
    }
    
    /**
     * @param null $order
     *
     * @return int
     */
    public static function getItemCount( $order = null ) : int {
        if( is_numeric( $order ) ) $order = wc_get_order( $order );
        if( empty( $order ) ) return 0;
        return $order->get_item_count();
    }
    
    /**
     * @param int $order_id
     *
     * @return string
     */
    public static function invoiceUrl( $order_id = 0 ) : string {
        return wp_nonce_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=".$order_id, 'generate_wpo_wcpdf' );
    }
    
    /**
     * @param null $order
     *
     * @return float|int
     */
    public static function referralFee( $order = null, $user_id = 0 ) {
        if( empty( $order ) ) return 0;
        if( is_numeric( $order ) ) {
            $order_id = $order;
            $order    = wc_get_order( $order_id );
        } else {
            if( !is_object( $order ) ) {
                return 0;
            }
        }
        $date = $order->get_date_paid() ?? '';
        if( empty( $date ) ) return 0;
        $date       = strtotime( $date );
        // TODO: check do we need to have a different multiplier for different user roles. We already have this functionality, but we have same multiplier for all for now
//        if(!empty($user_id) && MC_User_Functions::isRetailer($user_id)) {
//            $multiplier = 0.05;
//        } else {
//            $multiplier = $date > 1614553200 ? 0.05 : 0.02;
//        }
        $multiplier = $date > 1614553200 ? 0.05 : 0.02;
        $total      = $order->get_total();
        $shipping   = $order->get_shipping_total() ?? 0;
        $fees       = $order->get_total_fees() ?? 0;
        $total      = $total - $shipping - $fees;
        
        return number_format( $total * $multiplier, 2 );
    }
    
    /**
     * @param null  $order
     * @param false $discounted_products
     * @param bool  $fees
     *
     * @return float
     */
    public static function orderTotal( $order = null, $discounted_products = false, $fees = true ) : float {
        if( empty( $order ) ) return 0;
        if( is_numeric( $order ) ) {
            $order_id = $order;
            $order    = wc_get_order( $order_id );
            if( empty( $order ) ) return 0;
        } else {
            if( !is_object( $order ) ) {
                return 0;
            }
        }
        $items = $order->get_items();
        $total = 0;
        foreach( $items as $item ) {
            $quantity = $item->get_quantity();
            if( $discounted_products ) {
                $price = $item->get_total();
            } else {
                $product_id = $item->get_product_id();
                $product    = wc_get_product( $product_id );
                $price      = is_object( $product ) ? $product->get_price() ?? 6 : 6;
                $price = $price * $quantity;
            }
            $total = $total + $price;
        }
        if( !$fees ) return number_format( $total, 2 );
        $shipping = $order->get_shipping_total() ?? 0;
        $fees     = $order->get_total_fees() ?? 0;
        $total    = $total + $shipping + $fees;
        
        return number_format( $total, 2 );
    }
    
    /**
     * @param $bulk_actions
     *
     * @return mixed
     */
    public static function addBulkActions( $bulk_actions ) {
        $bulk_actions['mark_promotional'] = 'Mark Promotional Completed';
        $bulk_actions['mark_promotional'] = 'Mark Approved and Pending';
        
        return $bulk_actions;
    }
    
    public static function registerCustomStatuses() {
        register_post_status( 'wc-promotional', [
            'label'                     => _x( 'Completed Promotional', 'WooCommerce Order status', 'text_domain' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Completed Promotional (%s)', 'Completed Promotional (%s)', 'text_domain' ),
        ] );
        
        register_post_status( 'wc-approved-pending', [
            'label'                     => _x( 'Approved and Pending', 'WooCommerce Order status', 'text_domain' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Approved and Pending (%s)', 'Approved and Pending(%s)', 'text_domain' ),
        ] );
    }
    
    public static function wpblog_wc_add_order_statuses( $order_statuses ) {
        $order_statuses['wc-promotional']      = _x( 'Completed Promotional', 'WooCommerce Order status', MC_TEXT_DOMAIN );
        $order_statuses['wc-approved-pending'] = _x( 'Approved and Pending', 'WooCommerce Order status', MC_TEXT_DOMAIN );
        return $order_statuses;
    }
    
    public static function markPromotionalCompleted() {
        // if an array with order IDs is not presented, exit the function
        if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) ) {
            return;
        }
        
        foreach( $_REQUEST['post'] as $order_id ) {
            $order      = new WC_Order( $order_id );
            $order_note = 'That\'s what happened by bulk edit:';
            $order->update_status( 'promotion-completed', $order_note, true ); //
            
        }
        
        //using add_query_arg() is not required, you can build your URL inline
        $location = add_query_arg( [
                                       'post_type'          => 'shop_order',
                                       'marked_promotional' => 1, // markED_imported=1 is  the $_GET variable for notices
                                       'changed'            => count( $_REQUEST['post'] ), // number of changed orders
                                       'ids'                => join( $_REQUEST['post'],
                                                                     ',' ),
                                       'post_status'        => 'all',
                                   ],
                                   'edit.php' );
        
        wp_redirect( admin_url( $location ) );
        exit;
    }
    
    public static function noticePromotionalCompleted() {
        global $pagenow, $typenow;
        
        if( $typenow == 'shop_order'
            && $pagenow == 'edit.php'
            && isset( $_REQUEST['marked_promotional'] )
            && $_REQUEST['marked_promotional'] == 1
            && isset( $_REQUEST['changed'] ) ) {
            $message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $_REQUEST['changed'] ),
                                number_format_i18n( $_REQUEST['changed'] ) );
            echo "<div class=\"updated\"><p>{$message}</p></div>";
        }
    }
    
    /**
     * @param $order_id
     *
     * @return array
     */
    public static function getNotes( $order_id = 0 ) {
        remove_filter( 'comments_clauses', [ 'WC_Comments', 'exclude_order_comments' ] );
        $args = [
            'orderby' => 'comment_ID',
            'order'   => 'DESC',
            'approve' => 'approve',
            'type'    => 'order_note',
            'number'  => 400,
        ];
        if( !empty( $order_id ) ) $comments['post_id'] = $order_id;
        $comments = get_comments( $args );
        $notes    = wp_list_pluck( $comments, 'comment_content' );
        add_filter( 'comments_clauses', [ 'WC_Comments', 'exclude_order_comments' ] );
        return $notes;
    }
    
    /**
     * @param $order
     * @param $data
     */
    public static function contentCreatorOrderMeta( $order, $data ) {
        if( MC_User_Functions::isAdmin() ) return;
        $affiliates = isset( $_COOKIE['content_creator'] ) ? $_COOKIE['content_creator'] : [];
        if( empty( $affiliates ) ) return;
        $order->update_meta_data( '_content_creator', $affiliates );
    }
    
    /**
     * @param int $order_id
     */
    public static function complete( $order_id = 0 ) {
        $order = wc_get_order( $order_id );
        if( $order == null || $order == false ) return;
        $order->update_status( 'completed' );
    }
    
    /**
     * @param int $order_id
     *
     * @return array
     */
    public static function getOrderItemsFromId( $order_id = 0 ) {
        if( empty( $order_id ) ) return [];
        $order = wc_get_order( $order_id );
        return $order->get_items();
    }
    
    /**
     * @param $item
     *
     * @return bool
     */
    public static function productCollection( $item ) {
        if( !function_exists( 'wc_cp_is_composite_container_order_item' ) ) return false;
        if( wc_cp_is_composite_container_order_item( $item ) ) return true;
        return false;
    }
    
    /**
     * @param $item
     * @param $order
     *
     * @return bool
     */
    public static function productCollectionChild( $item, $order ) {
        if( !function_exists( 'wc_cp_is_composited_order_item' ) ) return false;
        if( wc_cp_is_composited_order_item( $item, $order ) ) return true;
        return false;
    }
    
    public static function getOrderTotals() {
        $totals = [];
        
        $location = !empty( $_GET['loc'] ) ?? 'us';
        
        if( $location != 'nl' ) {
            $query  = new WC_Order_Query( [
                                              'limit'        => 100,
                                              'status'       => 'processing',
                                              'order'        => 'ASC',
                                              'meta_key'     => '_shipping_country',
                                              'meta_compare' => '=',
                                              'meta_value'   => 'US',
                                          ] );
            $orders = $query->get_orders();
        } else {
            $query  = new WC_Order_Query( [
                                              'limit'        => -1,
                                              'status'       => 'processing',
                                              'order'        => 'ASC',
                                              'meta_key'     => '_shipping_country',
                                              'meta_compare' => '!=',
                                              'meta_value'   => 'US',
                                          ] );
            $orders = $query->get_orders();
            foreach( $orders as $key => $order ) {
                $order_id = $order->get_id();
                if( !empty( get_post_meta( $order_id, 'mc_order_printed', true ) ) ) unset( $orders[ $key ] );
            }
        }
        
        $totals['to_process'] = count( $orders );
        return $totals;
    }
    
    /**
     * @param $item
     *
     * @return bool
     */
    public static function order_product_collection( $item ) {
        if( !function_exists( 'wc_cp_is_composite_container_order_item' ) ) return false;
        if( wc_cp_is_composite_container_order_item( $item ) ) return true;
        return false;
    }
    
    /**
     * @param $item
     * @param $order
     *
     * @return bool
     */
    public static function order_product_collection_child( $item, $order ) {
        if( !function_exists( 'wc_cp_is_composited_order_item' ) ) return false;
        if( wc_cp_is_composited_order_item( $item, $order ) ) return true;
        return false;
    }
    
    /**
     * Clear affiliate cookies after order is created
     */
    public static function clearAffiliateCookies() {
        if( isset( $_COOKIE['content_creator'] ) ) {
            unset( $_COOKIE['content_creator'] );
            setcookie( 'content_creator', null, -1, '/' );
        }
    }
    
    /**
     * @param string $user_id
     *
     * @return bool
     */
    public static function userHasPreviouslyPurchased( $user_id = '' ) : bool {
        if( empty( $user_id ) ) $user_id = MC_User_Functions::id();
        $user = get_user_by( 'ID', $user_id );
        if( empty( $user ) ) return false;
        $backer = self::emailHasPreviouslyPurchased( $user->user_email );
        if( !empty( $backer ) ) return true;
        $args      = [
            'numberposts' => 1, // one order is enough
            'meta_key'    => '_customer_user',
            'meta_value'  => $user_id,
            'post_type'   => 'shop_order',
            'return'      => 'ids',
        ];
        $numorders = count( wc_get_orders( $args ) );
        return $numorders > 0;
    }
    
    /**
     * @param string $email
     *
     * @return bool
     */
    public static function emailHasPreviouslyPurchased( $email = '' ) : bool {
        if( !is_user_logged_in() ) return false;
        $current_user = wp_get_current_user();
        if( empty( $current_user ) ) return false;
        $user_id   = $current_user->ID;
        $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user_id, 'user' );
        if( !empty( $backer_id ) ) return true;
        foreach( [ 51, 52 ] as $product_id ) {
            $bought = wc_customer_bought_product( $email, $current_user->ID, $product_id );
            if( !empty( $bought ) ) return true;
        }
        return false;
    }
    
    /**
     * @param $user_id
     * @param $product_id
     *
     * @return bool
     */
    public static function userIdHasBoughtProduct( $product_id, $user_id = 0 ) {
        if( empty( $user_id ) ) $user_id = MC_User_Functions::id();
        $user = get_user_by( 'ID', $user_id );
        if( empty( $user ) ) return false;
        $email = $user->user_email;
        return wc_customer_bought_product( $email, $user_id, $product_id );
    }
    
    public static function yearlySalesByState() {
        $year        = $_GET['year'] ?? date( "Y" );
        $domain      = $_SERVER['HTTP_HOST'];
        $path        = $_SERVER['SCRIPT_NAME'];
        $queryString = $_SERVER['QUERY_STRING'];
        $url         = "https://".$domain.$path."?".$queryString;
        
        $x = 2019; // First year active;
        while( $x <= date( "Y" ) ) {
            $link = '<a href="'.$url.'&year='.$x.'" style="margin: 0.5rem;">'.$x.'</a>';
            if( $x == $year ) $link = '<strong>'.$link.'</strong>';
            echo $link;
            $x++;
        }
        
        $output = get_option( 'mc_orders_states_'.$year );
        if( !empty( $output ) ) echo $output;
    }
    
    public static function saveOrdersByStates() {
        $year = 2019;
        while( $year <= date( "Y" ) ) {
            $args     = [
                'post_type'      => 'shop_order',
                'posts_per_page' => '-1',
                'year'           => $year,
                'post_status'    => [ 'wc-completed' ],
                'meta_query'     => [
                    [
                        'key'   => '_billing_country',
                        'value' => 'US',
                    ],
                ],
            ];
            $my_query = new WP_Query( $args );
            $orders   = $my_query->posts;
            
            $states = MC_Geo::getStates();
            foreach( $states as $key => $state ) {
                $states[ $key ] = [
                    'order_count' => 0,
                    'order_total' => 0,
                    'name'        => $state,
                
                ];
            }
            
            foreach( $orders as $order ) {
                $order_id   = $order->ID;
                $order      = wc_get_order( $order_id );
                $order_data = $order->get_data();
                
                if( $order_data['billing']['country'] === 'US' ) {
                    foreach( $states as $state_code => $state ) {
                        if( $order_data['billing']['state'] === $state_code ) {
                            $states[ $state_code ]['order_count'] += 1;
                            $states[ $state_code ]['order_total'] += $order->get_total();
                        }
                    }
                }
            }
            
            $html = "<h3>Sales by State for Year ".$year."</h3>";
            $html .= '<table class="widefat fixed" cellspacing="0"><thead><tr><td>State Code</td><td>State Name</td><td>Orders</td><td>Value ($)</td></tr></thead><tbody>';
            foreach( $states as $state_code => $state ) {
                $html .= "<tr><td>$state_code</td><td>".$state['name']."</td><td>".$state['order_count']."</td><td>".$state['order_total']."</td></tr>";
            }
            $html .= '</tbody></table>';
            update_option( 'mc_orders_states_'.$year, $html );
            $year++;
        }
    }
    
    /**
     * @param array $statuses
     *
     * @return array
     */
    public static function get_all_site_order_ids( $statuses = [] ) : array {
        return get_posts( [
                              'numberposts' => -1,
                              'post_type'   => wc_get_order_types(),
                              'post_status' => !empty( $statuses ) ? $statuses : array_keys( wc_get_order_statuses() ),
                              'fields'      => 'ids',
                          ] );
    }
    
    /**
     * @param       $email
     * @param array $statuses
     *
     * @return array
     */
    public static function get_order_ids_by_email( $email = '', $statuses = [] ) : array {
        if( empty( $email ) ) return [];
        return get_posts( [
                              'numberposts' => -1,
                              'post_type'   => wc_get_order_types(),
                              'post_status' => !empty( $statuses ) ? $statuses : array_keys( wc_get_order_statuses() ),
                              'meta_query' => [
                                  [
                                      'key'   => '_billing_email',
                                      'value' => $email
                                  ]
                              ],
                              'fields'     => 'ids',
                          ] );
    }
    
    /**
     *
     * Gets all orders for the current site
     *
     * @return array
     */
    public static function get_all_site_order_up_to_july() : array {
        return get_posts( [
                              'numberposts' => -1,
                              'post_type'   => wc_get_order_types(),
                              'post_status' => array_keys( wc_get_order_statuses() ),
                              'fields'      => 'ids',
                              'date_query'  => [
                                  [ 'before' => 'August 1st, 2021' ]
                              ]
                          ] );
    }
    
}