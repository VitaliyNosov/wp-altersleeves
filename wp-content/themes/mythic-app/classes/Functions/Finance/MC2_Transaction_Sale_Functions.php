<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_Transaction_Type_Functions;

/**
 * Class MC2_Transaction_Sale_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Transaction_Sale_Functions extends MC2_Transaction_Type_Functions {

    public static $transaction_key = 'sale';

    public function __construct( &$args ) {
        $this->prepareAssociatedOrder( $args );
        parent::__construct( $args );
    }

    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_id'] );
    }

    public function prepareDate( &$args ) : string {
        return $this->associated_order->get_date_paid() ?? $this->associated_order->get_date_created();
    }

}