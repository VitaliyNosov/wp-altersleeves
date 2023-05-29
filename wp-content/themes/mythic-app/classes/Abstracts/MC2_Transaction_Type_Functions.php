<?php

namespace Mythic\Abstracts;

use Mythic\Functions\Finance\MC2_Transaction_Functions;
use Mythic\Helpers\MC2_Vars;
use WC_Coupon;

/**
 * Class MC2_Transaction_Type_Functions
 *
 * @package Mythic\Abstracts
 */
abstract class MC2_Transaction_Type_Functions {

    public static $transaction_key = '';
    public $associated_user = null;
    public $associated_order = null;
    public $associated_product = null;
    public $associated_coupon = null;
    public $associated_withdrawal = null;

    public function __construct( &$args ) {
        if( empty( $this->checkIfTransactionNecessary( $args ) ) ) return null;
        $data = [
            'type'       => $this->prepareTransactionKey(),
            'action_id'  => $this->prepareActionId(),
            'date'       => $this->prepareDate( $args ),
            'value'      => $this->prepareValue( $args ),
            'details'    => $this->prepareDetails( $args ),
            'message'    => $this->prepareMessage( $args ),
            'user_id'    => $this->prepareUserId( $args ),
            'order_id'   => $this->prepareOrderId( $args ),
            'product_id' => $this->prepareProductId( $args ),
            'currency'   => $this->prepareCurrency( $args ),
        ];

        $this->writeTransactionData( $data );

        return (object) $data;
    }

    public function checkIfTransactionNecessary( &$args ) {
        return false;
    }

    private function writeTransactionData( $data ) {
        global $wpdb;
        $table_name = MC2_Transaction_Functions::$table_name;
        $format     = [ '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%d', '%d', '%s', ];

        return $wpdb->insert( $table_name, $data, $format );
    }

    public function prepareTransactionKey() : string {
        return static::$transaction_key;
    }

    public function prepareActionId() : string {
        return MC2_Vars::generate( 15 );
    }

    public function prepareDate( &$args ) : string {
        return $args['date'] ?? date( 'Y-m-d H:i:s' );
    }

    public function prepareValue( &$args ) : float {
        return (float) $args['value'] ?? 0.00;
    }

    public function prepareDetails( &$args ) : string {
        return $args['details'] ?? '';
    }

    public function prepareMessage( &$args ) : string {
        return $args['message'] ?? '';
    }

    public function prepareUserId( &$args ) : int {
        return $args['user_id'] ?? 0;
    }

    public function prepareOrderId( &$args ) : int {
        return $args['order_id'] ?? 0;
    }

    public function prepareProductId( &$args ) : int {
        return $args['product_id'] ?? 0;
    }

    public function prepareCurrency( &$args ) : string {
        return $args['currency'] ?? 'USD';
    }

    public function prepareAssociatedUser( &$args ) {
        if( $this->associated_user === false ) return;

        if( empty( $args['user_id'] ) && empty( $args['user_data'] ) ) {
            $this->associated_user = false;

            return;
        }

        if( empty( $args['user_data'] ) ) {
            $args['user_data'] = get_user_by( 'ID', $args['user_id'] );
        }

        if( empty( $args['user_data'] ) ) {
            $args['user_id']       = 0;
            $this->associated_user = false;

            return;
        }

        $this->associated_user = $args['user_data'];
    }

    public function prepareAssociatedOrder( &$args ) {
        if( $this->associated_order === false ) return;

        if( empty( $args['order_id'] ) && empty( $args['order_data'] ) ) {
            $this->associated_order = false;

            return;
        }

        if( empty( $args['order_data'] ) ) {
            $args['order_data'] = wc_get_order( $args['order_id'] );
        }

        if( empty( $args['order_data'] ) ) {
            $args['order_id']       = 0;
            $this->associated_order = false;

            return;
        }

        $this->associated_order = $args['order_data'];
    }

    public function prepareAssociatedProduct( &$args ) {
        if( $this->associated_product === false ) return;

        if( empty( $args['product_id'] ) && empty( $args['product_data'] ) ) {
            $this->associated_product = false;

            return;
        }

        if( empty( $args['product_data'] ) ) {
            $args['product_data'] = wc_get_product( $args['product_id'] );
        }

        if( empty( $args['product_data'] ) ) {
            $args['product_id']       = 0;
            $this->associated_product = false;

            return;
        }

        $this->associated_product = $args['product_data'];
    }

    public function prepareAssociatedCoupon( &$args ) {
        if( $this->associated_coupon === false ) return;

        if( empty( $args['coupon_id'] ) && empty( $args['coupon_code'] ) && empty( $args['coupon_data'] ) ) {
            $this->associated_coupon = false;

            return;
        }

        if( empty( $args['coupon_code'] ) && empty( $args['coupon_data'] ) ) {
            $args['coupon_code'] = wc_get_coupon_code_by_id( $args['coupon_id'] );
        }

        if( !empty( $args['coupon_code'] ) && empty( $args['coupon_data'] ) ) {
            $args['coupon_data'] = new WC_Coupon( $args['coupon_code'] );
        }

        if( empty( $args['coupon_data']->get_date_created() ) ) {
            $this->associated_coupon = false;

            return;
        }

        $this->associated_coupon = $args['coupon_data'];
    }

    public function prepareAssociatedWithdrawal( &$args ) {
        if( $this->associated_withdrawal === false ) return;

        $this->associated_withdrawal = 0;
    }

    public static function getTransactionData( $id ) {
        return MC2_Transaction_Functions::getTransactionData( $id );
    }

    public static function getTransactionsData( $user_id = 0, $order_id = 0, $product_id = 0, $limit = 0, $offset = 0 ) {
        return MC2_Transaction_Functions::getTransactionsData( static::$transaction_key, $user_id, $order_id, $product_id, $limit, $offset );
    }

}