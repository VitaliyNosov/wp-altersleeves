<?php

namespace Mythic_Core\Functions;

use DateTime;
use Mythic_Core\Objects\MC_Withdrawal;
use Mythic_Core\Utils\MC_Vars;

/**
 * Class MC_Withdrawal_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Withdrawal_Functions {
    
    /**
     * @return string
     */
    public static function compileWithdrawals() : string {
        if( get_current_blog_id() != 1 ) return '';
        if( date( 'D' ) == 'Sat' || date( 'D' ) == 'Sun' ) return '';
        
        global $wpdb;
        
        $withdrawals = self::getUnexportedWithdrawals();
        if( empty( $withdrawals ) ) return '';
        
        $path = ABSPATH.'/files/withdrawals';
        if( !is_dir( $path ) ) mkdir( $path, 0755, true );
        $time = time();
        
        $fp = fopen( ABSPATH.'/files/withdrawals/'.$time.'.csv', 'w' );
        
        $csv = [
            [
                'name',
                'recipientEmail',
                'paymentReference',
                'amountCurrency',
                'amount',
                'sourceCurrency',
                'targetCurrency',
                'type',
                'paysFee',
            ],
        ];
        
        foreach( $withdrawals as $withdrawal ) {
            if( $withdrawal->source == 'paypal' ) continue;
            $idCreator = $withdrawal->creator_id;
            $name      = get_user_meta( $idCreator, 'mc_transferwise_name', true );
            $email     = get_user_meta( $idCreator, 'mc_transferwise_email', true );
            $currency  = get_user_meta( $idCreator, 'mc_transferwise_currency', true );
            $fee       = MC_User_Functions::isContentCreator( $idCreator ) ? 'no' : 'yes';
            $csv[]     = [
                $name,
                $email,
                $withdrawal->transaction_id,
                'source',
                $withdrawal->paid_init,
                'USD',
                $currency,
                'EMAIL',
                $fee,
            ];
            $wpdb->update( 'mc_withdrawals', [ 'exported' => $time ], [ 'id' => $withdrawal->id ] );
        }
        
        foreach( $csv as $fields ) {
            fputcsv( $fp, $fields );
        }
        fclose( $fp );
        
        $file = ABSPATH.'/files/withdrawals/'.$time.'.csv';
        wp_mail( 'james@altersleeves.com', 'Withdrawals', 'New', [], $file );
        wp_mail( 'payablesusa@altersleeves.com', 'Withdrawals', 'New', [], $file );
        
        return $file;
    }
    
    /**
     * @return array|object
     */
    public static function getUnexportedWithdrawals() {
        global $wpdb;
        $table_name = MC_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE exported = 0';
        $results    = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @return array|object
     */
    public static function getAll() {
        global $wpdb;
        $table_name = MC_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name;
        $results    = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param int $idTransaction
     *
     * @return bool
     */
    public static function exists( $idTransaction = 0 ) {
        if( empty( $idTransaction ) ) return false;
        global $wpdb;
        $table_name = MC_Withdrawal::TABLE_NAME;
        $query      = "SELECT * FROM $table_name WHERE ".MC_Withdrawal::DB_TRANSACTION_ID." = $idTransaction";
        $results    = $wpdb->get_results( $query, ARRAY_A );
        if( !empty( $results ) ) return true;
        
        return false;
    }
    
    /**
     * @param string $order
     *
     * @return array|object
     */
    public static function getAllOrderByDate( $order = 'DESC' ) {
        global $wpdb;
        $table_name = MC_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE creator_id != 0 ORDER BY date '.$order;
        $results    = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param int $idCreator
     *
     * @return array|object
     */
    public static function getByCreatorId( $idCreator = 0 ) {
        if( empty( $idCreator ) || !is_numeric( $idCreator ) ) return [];
        global $wpdb;
        $table_name = MC_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE '.MC_Withdrawal::DB_CREATOR_ID.'='.$idCreator;
        $query      .= ' ORDER BY date DESC';
        $results    = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param int $idCreator
     *
     * @return mixed
     */
    public static function getTotalWithdrawn( $idCreator = 0 ) {
        global $wpdb;
        $table_name = MC_Withdrawal::TABLE_NAME;
        $query      = 'SELECT sum(paid_init) as total_withdrawn FROM '.$table_name.' WHERE creator_id > 0';
        if( !empty( $idCreator ) ) $query .= ' AND creator_id = "'.trim( $idCreator ).'"';
        $result = $wpdb->get_results( $query )[0]->total_withdrawn;
        
        return number_format( $result, 2, '.', '' );
    }
    
    /**
     * @param int    $idCreator
     * @param int    $amount
     * @param string $currency
     * @param string $name
     * @param string $email
     * @param string $source
     *
     * @return bool
     */
    public static function request( $idCreator = 0, $amount = 0, $currency = 'USD', $name = '', $email = '' , $source = 'withdrawal' ) : bool {
        if( empty( $idCreator ) || empty( $amount ) ) return false;
        
        $user = get_user_by( 'ID', $idCreator );
        if( empty( $name ) ) {
            $name = get_user_meta( $idCreator, 'mc_transferwise_name', true );
            $name = !empty( $name ) ? $name : $user->first_name.' '.$user->first_name;
        }
        if( empty( $email ) ) {
            $email = get_user_meta( $idCreator, 'mc_transferwise_email', true );
            $email = !empty( $email ) ? $email : $user->user_email;
        }
        if( empty( $currency ) ) {
            $pre_currency = get_user_meta( $idCreator, 'mc_transferwise_currency', true );
            $currency     = !empty( $pre_currency ) ? $pre_currency : $currency;
        }
        
        $balance = MC_Creator_Functions::getAffiliateBalance( $idCreator );
        $amount  = (float) number_format( $amount, 2 );
        if( empty( $name ) || empty( $email ) || empty( $currency ) || $amount > $balance ) {
            ob_start(); ?>
            Creator ID: <?= $idCreator ?><br>
            Email: <?= $email ?><br>
            Name: <?= $name ?><br>
            Currency: <?= $currency ?><br>
            Amount requested: <?php var_dump( $amount ) ?><br>
            Balance: <?php var_dump( $balance ) ?><br><br>

            <strong>Reason:</strong><br>

            No name - <?php var_dump( empty( $name ) ) ?><br>
            No email - <?php var_dump( empty( $email ) ) ?><br>
            No currency - <?php var_dump( empty( $currency ) ) ?><br>
            Amount > Balance - <?php var_dump( $amount > $balance ) ?>
            <?php
            $output = ob_get_clean();
            wp_mail( 'james@altersleeves.com', 'Error withdrawing', $output );
            return false;
        }
        update_user_meta( $idCreator, 'mc_transferwise_name', $name );
        update_user_meta( $idCreator, 'mc_transferwise_email', $email );
        update_user_meta( $idCreator, 'mc_transferwise_currency', $currency );
        $date       = new DateTime();
        $reference  = $date->format( 'Ymd-' ).MC_Vars::generate( 10 );
        $date       = $date->format( "Y-m-d H:i:s" );
        $withdrawal = new MC_Withdrawal();
        $withdrawal->setTransactionId( $reference );
        $withdrawal->setIdCreator( $idCreator );
        $withdrawal->setPaidInit( $amount );
        $withdrawal->setCurrency( $currency );
        $withdrawal->setSource( $source );
        $withdrawal->setDate( $date );
        if( $source != 'withdrawal' ) {
            $withdrawal->setExported(1);
            $withdrawal->setApproved(1);
        }
        $withdrawal->create();
        
        $withdrawal_args = [
            'currency' => $currency,
            'user_id'  => $idCreator,
            'value'    => $amount,
        ];
        MC_Transaction_Functions::createTransactions( 'withdrawal', $withdrawal_args );
        return true;
    }
    
}