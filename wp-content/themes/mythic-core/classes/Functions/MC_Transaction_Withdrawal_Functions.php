<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Transaction_Type_Functions;

/**
 * Class MC_Transaction_Withdrawal_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Transaction_Withdrawal_Functions extends MC_Transaction_Type_Functions {
    
    public static $transaction_key = 'withdrawal';
    
    public function __construct( &$args ) {
        parent::__construct( $args );
    }
    
    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['user_id'] );
    }
    
}