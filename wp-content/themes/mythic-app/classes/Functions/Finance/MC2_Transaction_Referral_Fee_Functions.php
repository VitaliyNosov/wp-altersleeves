<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_Transaction_Type_Functions;

/**
 * Class MC2_Transaction_Referral_Fee_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Transaction_Referral_Fee_Functions extends MC2_Transaction_Type_Functions {

    public static $transaction_key = 'referral_fee';
    public $referral_fee = 0.00;

    public function __construct( &$args ) {
        $this->prepareAssociatedOrder( $args );
        $this->prepareAssociatedUser( $args );
        parent::__construct( $args );
    }

    public function checkIfTransactionNecessary( &$args ) {
        return !empty( $args['order_id'] );
    }

    public function prepareValue( &$args ) : float {
        $fee = MC2_Order_Functions::referralFee( $this->associated_order );
        $fee = number_format( $fee, 2 );
        $this->setReferralFee( $fee );

        return $fee;
    }

    public function prepareDate( &$args ) : string {
        return $this->associated_order->get_date_paid();
    }

    public function prepareCurrency( &$args ) : string {
        return $this->associated_order->get_currency();
    }

    public function prepareMessage( &$args ) : string {
        return 'Referral Fee of $'.$this->referralFee().' for '.$this->associated_user->data->display_name;
    }

    /**
     * @return float
     */
    public function referralFee() : float {
        return $this->referral_fee;
    }

    /**
     * @param float $referral_fee
     */
    public function setReferralFee( float $referral_fee ) {
        $this->referral_fee = $referral_fee;
    }

}