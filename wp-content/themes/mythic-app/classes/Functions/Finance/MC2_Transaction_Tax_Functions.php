<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_Transaction_Type_Functions;

/**
 * Class MC2_Transaction_Tax_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Transaction_Tax_Functions extends MC2_Transaction_Type_Functions {

    public static $transaction_key = 'tax';

    // TODO: associated order?
    public function __construct( &$args ) {
        parent::__construct( $args );
        $this->prepareAssociatedOrder( $args );
    }

    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_data'] ) && !empty( $args['order_item'] );
    }

    public function prepareValue( &$args ) : float {
        return number_format( $args['order_item']->get_total_tax(), 2 );
    }

}