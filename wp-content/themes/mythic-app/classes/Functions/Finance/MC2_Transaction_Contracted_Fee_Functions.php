<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_Transaction_Type_Functions;

/**
 * Class MC2_Transaction_Contracted_Fee
 *
 * @package Mythic\Functions
 */
class MC2_Transaction_Contracted_Fee_Functions extends MC2_Transaction_Type_Functions {

    public static $transaction_key = 'contracted_fee';

    public function __construct( &$args ) {
        parent::__construct( $args );
        $this->prepareAssociatedUser( $args );
    }

    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['user_id'] );
    }

}