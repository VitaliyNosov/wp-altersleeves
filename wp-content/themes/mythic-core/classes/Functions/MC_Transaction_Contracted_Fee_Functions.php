<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Transaction_Type_Functions;

/**
 * Class MC_Transaction_Contracted_Fee
 *
 * @package Mythic_Core\Functions
 */
class MC_Transaction_Contracted_Fee_Functions extends MC_Transaction_Type_Functions {
    
    public static $transaction_key = 'contracted_fee';
    
    public function __construct( &$args ) {
        parent::__construct( $args );
        $this->prepareAssociatedUser( $args );
    }
    
    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['user_id'] );
    }
    
}