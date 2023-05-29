<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_Transaction_Type_Functions;
use Mythic\Functions\Store\MC2_Order_Functions;

/**
 * Class MC2_Transaction_Order_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Transaction_Order_Functions extends MC2_Transaction_Type_Functions {

    public static $transaction_key = 'order';

    public function __construct( &$args ) {
        $this->prepareAssociatedOrder( $args );
        parent::__construct( $args );
    }

    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_id'] );
    }

    public function prepareValue( &$args ) : float {
        return MC2_Order_Functions::orderTotal( $this->associated_order );
    }

    public function prepareDate( &$args ) : string {
        return $this->associated_order->get_date_paid();
    }

    public function prepareOrderId( &$args ) : int {
        return $args['order_id'];
    }

    public function prepareUserId( &$args ) : int {
        return $this->associated_order->get_user_id() ?? 0;
    }

    public function prepareCurrency( &$args ) : string {
        return $this->associated_order->get_currency();
    }

}