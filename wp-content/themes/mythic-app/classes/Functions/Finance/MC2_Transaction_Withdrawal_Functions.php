<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_Transaction_Type_Functions;

/**
 * Class MC2_Transaction_Withdrawal_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Transaction_Withdrawal_Functions extends MC2_Transaction_Type_Functions {

    public static $transaction_key = 'withdrawal';

    public function __construct( &$args ) {
        parent::__construct( $args );
    }

    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['user_id'] );
    }

}