<?php

namespace Mythic\Functions\Finance;

use Mythic\Functions\Access\MC2_Environment;
use Mythic\Functions\MC2_Royalty_Functions;
use Mythic\Functions\User\MC2_User_Functions;
use Mythic\Functions\Store\MC2_Order_Functions;
use Mythic\Functions\Wordpress\MC2_WP;

/**
 * Class MC2_Transaction_Functions
 * @package Mythic\Functions
 */
class MC2_Transaction_Functions {

    public static $table_name = 'mc_transactions';
    public static $type_labels = [
        'cost'           => 'Business Costs',
        'contracted_fee' => 'Contracted Fee',
        'discount'       => 'Discount',
        'fee'            => 'Fee',
        'misc'           => 'Miscellaneous',
        'order'          => 'Order',
        'referral_fee'   => 'Referral fee',
        'refund'         => 'Refund',
        'royalty'        => 'Royalty',
        'sale'           => 'Sale',
        'tax'            => 'Tax',
        'withdrawal'     => 'Withdrawal',
    ];

    public static function getTransactionData( $id ) {
        global $wpdb;
        $table_name = static::$table_name;
        $id         = intval( $id );
        $query      = "SELECT * FROM $table_name WHERE id = $id";

        return $wpdb->get_row( $query );
    }

    public static function getTransactionsData( $type = '', $user_id = 0, $order_id = 0, $product_id = 0, $limit = 0,
        $offset = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;

        $query        = "SELECT * FROM $table_name";
        $where_or_and = 'WHERE';

        if( !empty( $type ) ) {
            $compare = 'LIKE \'';
            if( is_array( $type ) ) {
                $type    = implode( "', '", $type );
                $compare = 'IN (\'';
            }
            $query        .= " $where_or_and type $compare$type";
            $query        .= $compare == 'IN (\'' ? '\')' : "'";
            $where_or_and = 'AND';
        }
        if( !empty( $user_id ) && MC2_User_Functions::isAdmin() ) {
            $compare = '=';
            if( is_array( $user_id ) ) {
                $user_id = implode( ', ', $user_id );
                $compare = 'IN (\'';
            }
            $query        .= " $where_or_and user_id $compare$user_id";
            $query        .= $compare == 'IN (\'' ? ')' : '';
            $where_or_and = 'AND';
        }
        if( !empty( $order_id ) ) {
            $compare = '=';
            if( is_array( $order_id ) ) {
                $order_id = implode( ', ', $order_id );
                $compare  = 'IN (\'';
            }
            $query        .= " $where_or_and order_id $compare$order_id";
            $query        .= $compare == 'IN (\'' ? ')' : '';
            $where_or_and = 'AND';
        }
        if( !empty( $product_id ) ) {
            $compare = '=';
            if( is_array( $product_id ) ) {
                $product_id = implode( ', ', $product_id );
                $compare    = 'IN (\'';
            }
            $query .= " $where_or_and product_id $compare$product_id";
            $query .= $compare == 'IN (\'' ? ')' : '';
        }

        $query .= " ORDER BY id DESC";
        if( !empty( $limit ) ) {
            $query .= " LIMIT $offset, $limit";
        }

        return $wpdb->get_results( $query );
    }

    public static function getTransactionsDataCount( $type = '', $user_id = 0, $order_id = 0, $product_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;

        $query        = "SELECT COUNT(*) FROM $table_name";
        $where_or_and = 'WHERE';

        if( !empty( $type ) ) {
            $compare = 'LIKE \'';
            if( is_array( $type ) ) {
                $type    = implode( "', '", $type );
                $compare = 'IN (\'';
            }
            $query        .= " $where_or_and type $compare$type";
            $query        .= $compare == 'IN (\'' ? '\')' : "'";
            $where_or_and = 'AND';
        }
        if( !empty( $user_id ) && MC2_User_Functions::isAdmin() ) {
            $compare = '=';
            if( is_array( $user_id ) ) {
                $user_id = implode( ', ', $user_id );
                $compare = 'IN (\'';
            }
            $query        .= " $where_or_and user_id $compare$user_id";
            $query        .= $compare == 'IN (\'' ? ')' : '';
            $where_or_and = 'AND';
        }
        if( !empty( $order_id ) ) {
            $compare = '=';
            if( is_array( $order_id ) ) {
                $order_id = implode( ', ', $order_id );
                $compare  = 'IN (\'';
            }
            $query        .= " $where_or_and order_id $compare$order_id";
            $query        .= $compare == 'IN (\'' ? ')' : '';
            $where_or_and = 'AND';
        }
        if( !empty( $product_id ) ) {
            $compare = '=';
            if( is_array( $product_id ) ) {
                $product_id = implode( ', ', $product_id );
                $compare    = 'IN (\'';
            }
            $query .= " $where_or_and product_id $compare$product_id";
            $query .= $compare == 'IN (\'' ? ')' : '';
        }

        return $wpdb->get_var( $query );
    }

    /**
     * @param int $user_id
     * @return array|object|null
     */
    public static function getForAffiliateLedger( $user_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE user_id = $user_id AND type IN('referral_fee', 'withdrawal', 'contracted_fee', 'royalty' ) ORDER BY date DESC";
        return $wpdb->get_results( $query );
    }

    /**
     * @param int $order_id
     * @return array|object|null
     */
    public static function getOrderByOrderId( $order_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'order' AND order_id = $order_id";

        return $wpdb->get_results( $query );
    }

    /**
     * @return array
     */
    public static function getOrderIds() {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT order_id FROM $table_name  WHERE type = 'order'";

        return $wpdb->get_col( $query );
    }

    /**
     * @param int $order_id
     * @return array|object|null
     */
    public static function getRefundsByOrderId( $order_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'refund' AND order_id = $order_id";

        return $wpdb->get_results( $query );
    }

    public static function referralFeeByOrderIdAndUserId( $order_id = 0, $user_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'referral_fee' AND order_id = $order_id AND user_id = $user_id";

        return $wpdb->get_results( $query );
    }

    public static function getDiscountByOrderId( $order_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'discount' AND order_id = $order_id";

        return $wpdb->get_results( $query );
    }

    /**
     * @param int $order_id
     * @param string $message
     * @return array|object|null
     */
    public static function getFeeByOrderIdAndMessage( $order_id = 0, $message = '' ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'fee' AND order_id = $order_id AND message = '$message'";

        return $wpdb->get_results( $query );
    }

    /**
     * @param int $order_id
     * @param int $product_id
     * @return array|object|null
     */
    public static function getSalesByOrderIdAndProductId( $order_id = 0, $product_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'sale' AND order_id = $order_id AND product_id = '$product_id'";

        return $wpdb->get_results( $query );
    }

    /**
     * @param int $order_id
     * @param int $product_id
     * @return array|object|null
     */
    public static function getRoyaltiesByOrderIdAndProductId( $order_id = 0, $product_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'royalty' AND order_id = $order_id AND product_id = '$product_id'";

        return $wpdb->get_results( $query );
    }

    /**
     * @param int $date
     * @param int $user_id
     * @return array|object|null
     */
    public static function getWithdrawalsByDateAndUserId( $date = 0, $user_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'withdrawal' AND date = '$date' AND user_id = '$user_id'";

        return $wpdb->get_results( $query );
    }

    /**
     * @param int $date
     * @param int $user_id
     * @return array|object|null
     */
    public static function getContractedFeeByDateAndUserId( $date = 0, $user_id = 0 ) {
        global $wpdb;
        $table_name = static::$table_name;
        $query      = "SELECT * FROM $table_name WHERE type = 'contracted_fee' AND date = '$date' AND user_id = '$user_id'";

        return $wpdb->get_results( $query );
    }

    public static function createTransactionsAfterOrderCreated( $order ) {
        $args['order_data'] = $order;
        $keys               = [
            'cost',
            'contracted_fee',
            'discount',
            'fee',
            'misc',
            'order',
            'referral_fee',
            'royalty',
            'sale',
            'tax',
        ];
        $order_items        = $order->get_items();
        if( empty( $order_items ) ) return;
        foreach( $order_items as $order_item ) {
            $args['order_item'] = $order_item;
            self::createTransactions( $keys, $args );
        }
    }

    public static function createTransactions( $keys, $args ) {
        if( is_array( $keys ) ) {
            foreach( $keys as $key ) {
                self::createTransactionSingle( $key, $args );
            }
        } else {
            self::createTransactionSingle( $keys, $args );
        }
    }

    private static function createTransactionSingle( $key, &$args ) {
        switch( $key ) {
            case 'cost':
                return new MC2_Transaction_Business_Costs_Functions( $args );
            case 'contracted_fee':
                return new MC2_Transaction_Contracted_Fee_Functions( $args );
            case 'discount':
                return new MC2_Transaction_Discount_Functions( $args );
            case 'fee':
                return new MC2_Transaction_Fee_Functions( $args );
            case 'misc':
                return new MC2_Transaction_Miscellaneous_Functions( $args );
            case 'order':
                return new MC2_Transaction_Order_Functions( $args );
            case 'referral_fee':
                return new MC2_Transaction_Referral_Fee_Functions( $args );
            case 'refund':
                return new MC2_Transaction_Refund_Functions( $args );
            case 'royalty':
                return new MC2_Transaction_Royalty_Functions( $args );
            case 'sale':
                return new MC2_Transaction_Sale_Functions( $args );
            case 'tax':
                return new MC2_Transaction_Tax_Functions( $args );
            case 'withdrawal':
                return new MC2_Transaction_Withdrawal_Functions( $args );
        }

        return false;
    }

    /**
     * Creates additional table
     */
    public static function createTable() {
        global $wpdb;
        $table_name = static::$table_name;

        $create_table_query = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `type` VARCHAR(32) NOT NULL,
            `action_id` VARCHAR(32) NOT NULL,
            `date` DATETIME NOT NULL,
            `value` float(20,2) NOT NULL,
            `details` LONGTEXT,
            `message` LONGTEXT,
            `user_id` BIGINT(20) UNSIGNED NOT NULL,
            `order_id` BIGINT(20) UNSIGNED NOT NULL,
            `product_id` BIGINT(20) UNSIGNED NOT NULL,
            `loss` TINYINT (1) DEFAULT NULL
        ) {$wpdb -> get_charset_collate()};";
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );

        dbDelta( $create_table_query );
    }

    public static function updateTransactions() {
        // Orders
        self::updateOrders();
        // Withdrawals
        self::updateWithdrawals();
    }

    public static function updateWithdrawals() {
        $withdrawals = MC2_Withdrawal_Functions::getAll();
        foreach( $withdrawals as $withdrawal ) {
            $date    = $withdrawal->date;
            $user_id = $withdrawal->creator_id;
            if( !empty( self::getWithdrawalsByDateAndUserId( $date, $user_id ) ) ) continue;

            $withdrawal_args = [
                'currency' => $withdrawal->currency,
                'date'     => $date,
                'user_id'  => $user_id,
                'value'    => $withdrawal->paid_init,
            ];
            self::createTransactionSingle( 'withdrawal', $withdrawal_args );
        }
    }

    public static function updateOrders() {
        if( !MC2_Environment::primarySite() ) return;
        $filters = [
            'post_status'    => [ 'wc-processing', 'wc-completed', 'wc-refunded' ],
            'post_type'      => 'shop_order',
            'posts_per_page' => 300,
            'fields'         => 'ids',
            'post__not_in'   => self::getOrderIds(),
        ];
        $orders  = get_posts( $filters );
        foreach( $orders as $order_id ) {
            self::createTransactionsFromOrderId( $order_id );
        }
    }

    /**
     * @param int $order_id
     */
    public static function createTransactionsFromOrderId( $order_id = 0 ) {
        if( !empty( self::getOrderByOrderId( $order_id ) ) ) return;
        $mc_order    = new MC2_Order( $order_id );
        $promotional = $mc_order->promotion;
        $order       = wc_get_order( $order_id );
        $order_total = $order->get_total();
        $currency    = $order->get_currency();
        $symbol      = strtolower( $currency ) == 'eur' ? '€' : '$';

        if( empty( $order ) ) return;

        // Add Refund
        $refund_amount = $order->get_total_refunded() ?? 0;
        if( !empty( $refund_amount ) && empty( self::getRefundsByOrderId( $order_id ) ) ) self::createTransactionSingle( 'refund', $order_args );

        // Add Referral fees
        $order_referrals = [];
        if( !$promotional ) {
            $referrers_by_cookie = $mc_order->referrers_by_cookie;
            if( !empty( $referrers_by_cookie ) ) {
                $referrers_by_cookie = array_unique( $referrers_by_cookie );
                foreach( $referrers_by_cookie as $timestamp => $referrer_id ) {
                    $referral_args = [ 'order_id' => $order_id, 'user_id' => $referrer_id ];
                    if( !empty( self::referralFeeByOrderIdAndUserId( $order_id, $referrer_id ) ) ) continue;
                    $user = get_user_by( 'id', $referrer_id );
                    if( empty( $user ) ) continue;
                    $referral_amount   = MC2_Order_Functions::referralFee( $order_id );
                    $referral_amount   = number_format( $referral_amount, 2 );
                    $order_referrals[] = 'Referral fee of $'.$referral_amount.' for '.$user->display_name;
                    self::createTransactionSingle( 'referral_fee', $referral_args );
                }
            }
            $referrers_by_coupon = $mc_order->referrers_by_coupon;
            if( !empty( $referrers_by_coupon ) ) {
                $referrers_by_coupon = array_unique( $referrers_by_coupon );
                foreach( $referrers_by_coupon as $timestamp => $referrer_id ) {
                    $referral_args = [ 'order_id' => $order_id, 'user_id' => $referrer_id ];
                    if( !empty( self::referralFeeByOrderIdAndUserId( $order_id, $referrer_id ) ) ) continue;
                    $user = get_user_by( 'id', $referrer_id );
                    if( empty( $user ) ) continue;
                    $referral_amount   = MC2_Order_Functions::referralFee( $order_id );
                    $referral_amount   = number_format( $referral_amount, 2 );
                    $order_referrals[] = 'Referral fee of $'.$referral_amount.' for '.$user->display_name;
                    self::createTransactionSingle( 'referral_fee', $referral_args );
                }
            }
        }

        // Add Discounts
        $payment_method = $order->get_payment_method();
        $credits        = $payment_method == 'wdc_woo_credits';
        $user_id        = $order->get_user_id();
        $email          = $order->get_billing_email() ?? '';
        $legacy_backer  = false;
        if( !empty( $email ) ) {
            $legacy_backer = get_page_by_title( $email, ARRAY_A, 'backer' );
        }
        $discount       = $order->get_discount_total() ?? 0;
        $coupons        = $order->get_coupon_codes();
        $message        = '';
        $order_discount = '';
        $detail         = '';
        if( $credits || empty( $payment_method ) ) {
            if( $legacy_backer && !MC2_User_Functions::isAdmin( $user_id ) ) {
                $order_discount = $message = 'Kickstarter order paid for with credits.';
                $detail         = 'kickstarter';
            } else {
                $order_discount = $message = 'Promotional order paid for with credits.';
                $detail         = 'promotion';
            }
        } else {
            if( !empty( $coupons ) ) {
                $order_discount = $message = 'Discount of '.$symbol.$discount.' provided by the following coupons: '.implode( ', ', $coupons );
            }
        }
        if( ( !empty( $discount ) || $credits ) && empty( self::getDiscountByOrderId( $order_id ) ) ) {
            $discount_args = [
                'order_id' => $order_id,
                'message'  => $message,
                'total'    => $credits,
                'details'  => $detail,
            ];
            self::createTransactionSingle( 'discount', $discount_args );
        }

        // Fees
        if( !$credits ) {
            $order_fees = [];
            $fees       = $order->get_fees() ?? [];
            foreach( $fees as $fee ) {
                $name         = $fee->get_name() ?? '';
                $value        = $fee->get_total() ?? 0;
                $value        = number_format( $value, 2 );
                $order_fees[] = $name.' for '.$symbol.$value."\n";

                if( !empty( self::getFeeByOrderIdAndMessage( $order_id, $name ) ) ) continue;
                switch( $name ) {
                    case strpos( strtolower( $name ), 'paypal' ) !== false :
                        $detail = 'paypal';
                        break;
                    default :
                        $detail = '';
                        break;
                }

                $fee_args = [
                    'order_id' => $order_id,
                    'message'  => $name,
                    'value'    => $value,
                    'details'  => $detail,
                ];
                self::createTransactionSingle( 'fee', $fee_args );
            }
        }

        $shipping_total = $order->get_shipping_total();
        $order_shipping = '';
        if( !empty( $shipping_total ) ) {
            $shipping_name  = $order->get_shipping_method();
            $message        = 'Shipping Type: '.$shipping_name.' from the ';
            $country        = $order->get_shipping_country() ?? $order->get_billing_country() ?? '';
            $message        .= $message.$country == 'US' ? 'US Office.' : 'NL office';
            $order_shipping = 'Shipped with '.$message;
            if( empty( self::getFeeByOrderIdAndMessage( $order_id, $message ) ) ) {
                $fee_args = [
                    'order_id' => $order_id,
                    'message'  => $message,
                    'value'    => $shipping_total,
                    'details'  => 'shipping',
                ];
                self::createTransactionSingle( 'fee', $fee_args );
            }
        }

        // Sales
        $order_items = $order->get_items();
        foreach( $order_items as $order_item ) {
            $product_id = $order_item->get_product_id();
            $quantity   = $order_item->get_quantity();
            for( $x = 1; $x <= $quantity; $x++ ) {
                $preexisting = self::getSalesByOrderIdAndProductId( $order_id, $product_id );
                if( count( $preexisting ) >= $x ) break;
                $value = $order_item->get_total() ?? 0;
                if( !empty( $value ) ) $value = $value / $quantity;
                $sale_args = [
                    'order_id'   => $order_id,
                    'product_id' => $product_id,
                    'value'      => $value,
                    'details'    => $promotional ? 'promotion' : '',
                    'user_id'    => (int) MC2_WP::authorId( $product_id ),
                    'message'    => '('.$x.' of '.$quantity.') Sale of product: '.$product_id.')',
                ];
                self::createTransactionSingle( 'sale', $sale_args );
            }
        }

        if( !$promotional ) {
            // Royalties
            $order_items = $order->get_items();
            foreach( $order_items as $order_item ) {
                $product_id = $order_item->get_product_id();
                $quantity   = $order_item->get_quantity();

                $original_royalties = MC2_Royalty_Functions::getByOrderIdAndProductId( $order_id, $product_id );
                if( !empty( $original_royalties ) ) {
                    foreach( $original_royalties as $original_royalty ) {
                        $value = $original_royalty->value ?? 0;
                        $value = $value / $quantity;
                        for( $x = 1; $x <= $quantity; $x++ ) {
                            $preexisting = self::getRoyaltiesByOrderIdAndProductId( $order_id, $product_id );
                            if( count( $preexisting ) >= $x ) break;
                            $royalty_args = [
                                'order_id'   => $order_id,
                                'product_id' => $product_id,
                                'user_id'    => MC2_WP::authorId( $product_id ),
                                'value'      => $value,
                                'message'    => '('.$x.' of '.$quantity.') Royalty for product: '.$product_id.')',
                            ];
                            self::createTransactionSingle( 'royalty', $royalty_args );
                        }
                    }
                }

                for( $x = 1; $x <= $quantity; $x++ ) {
                    $preexisting = self::getRoyaltiesByOrderIdAndProductId( $order_id, $product_id );
                    if( count( $preexisting ) >= $x ) break;
                    $value        = MC2_Royalty_Functions::getProductValue( $product_id, $order_id );
                    $value        = !empty( $value ) ? $value['value'] / $quantity : 0;
                    $royalty_args = [
                        'order_id'   => $order_id,
                        'product_id' => $product_id,
                        'user_id'    => MC2_WP::authorId( $product_id ),
                        'value'      => $value,
                        'message'    => '('.$x.' of '.$quantity.') Royalty for product: '.$product_id.')',
                    ];
                    self::createTransactionSingle( 'royalty', $royalty_args );
                }
            }
        }

        $date          = $order->get_date_paid();
        $date          = date( "F j, Y, g:i a", strtotime( $date ) );
        $order_message = 'Order '.$order_id.' made on '.$date.' for '.$symbol.$order_total.".\n";
        if( !empty( $order_discount ) ) $order_message .= $order_discount.".\n";
        if( !empty( $order_fees ) ) $order_message .= implode( '', $order_fees );
        if( !empty( $order_shipping ) ) $order_message .= $order_shipping.".\n";
        if( !empty( $order_referrals ) ) $order_message .= implode( "\n", $order_referrals );

        $order_args = [
            'order_id' => $order_id,
            'details'  => $promotional ? 'promotion' : '',
            'message'  => $order_message,
        ];

        if( empty( self::getOrderByOrderId( $order_id ) ) ) self::createTransactionSingle( 'order', $order_args );
        $mc_order = null;
    }

}