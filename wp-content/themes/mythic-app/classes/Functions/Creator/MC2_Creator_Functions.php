<?php

namespace Mythic\Functions\Creator;

use Mythic\Functions\Finance\MC2_Withdrawal_Functions;
use Mythic\Functions\User\MC2_User_Functions;
use Mythic\Objects\MC2_Reward;

class MC2_Creator_Functions {

    /**
     * @param int   $idCreator
     * @param false $formatted
     *
     * @return bool|int|mixed|string
     */
    public static function getBalance( $idCreator = 0, $formatted = false ) {
        if( empty( $idCreator ) ) return 0;
        $totalEarned    = MC2_Royalty_Functions::getEarnedByAlteristId( $idCreator );
        $totalWithdrawn = MC2_Withdrawal_Functions::getTotalWithdrawn( $idCreator );
        $rewards        = new MC2_Reward();
        $rewards        = $rewards->getByUserId( $idCreator );
        $reward         = 0;
        if( !empty( $rewards ) ) $reward = $rewards[0]->amount;

        $availableBalance = $totalEarned - $totalWithdrawn + $reward;
        $deductions       = MC2_User_Functions::meta( 'mc_deductions', $idCreator );
        if( !empty( $deductions ) ) $availableBalance = $availableBalance - $deductions;
        if( !$formatted ) return $availableBalance;
        return number_format( $availableBalance, 2 );
    }

    /**
     * @param int $idCreator
     *
     * @return string
     */
    public static function emailWise( $idCreator = 0 ) {
        if( empty( $idCreator ) || !get_userdata( $idCreator ) ) return '';

        return MC2_User_Functions::meta( 'mc_email_transferwise', $idCreator );
    }

}