<?php

namespace Mythic\Functions\Finance;

use Mythic\Objects\Finance\MC2_Withdrawal;
use phpmailerException;

/**
 * Class MC2_Withdrawal_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Withdrawal_Functions {

    public static function sendWithdrawals() {
        $csv = self::compileWithdrawals();
        if( empty( $csv ) || !file_exists( $csv ) ) return;
    }

    /**
     * @param bool $unexported
     *
     * @return string
     * @throws phpmailerException
     */
    public static function compileWithdrawals( $unexported = true ) : string {
        if( get_current_blog_id() != 1 ) return '';
        if( date( 'D' ) == 'Sat' || date( 'D' ) == 'Sun' ) return '';

        global $wpdb;

        $withdrawals = $unexported ? self::getUnexportedWithdrawals() : self::getExportedWithdrawals();
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
            $fee       = MC2_Content_Creator::is( $idCreator ) ? 'no' : 'yes';
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
        $table_name = MC2_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE exported = 0';
        $results    = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];

        return $results;
    }

    /**
     * @return array|object
     */
    public static function getExportedWithdrawals() {
        global $wpdb;
        $table_name = MC2_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE exported != 0';
        $results    = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];

        return $results;
    }

    /**
     * @param string $date
     *
     * @return false|string
     */
    public static function dbStrToDate( $date = '' ) {
        if( empty( $date ) ) return '';
        $date      = str_replace( '/', '-', $date );
        $timestamp = strtotime( $date );
        if( empty( $timestamp ) ) return '';

        return date( "Y-m-d H:i:s", $timestamp );
    }

    /**
     * @return array|object
     */
    public static function getAll() {
        global $wpdb;
        $table_name = MC2_Withdrawal::TABLE_NAME;
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
        $table_name = MC2_Withdrawal::TABLE_NAME;
        $query      = "SELECT * FROM $table_name WHERE ".MC2_Withdrawal::DB_TRANSACTION_ID." = $idTransaction";
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
        $table_name = MC2_Withdrawal::TABLE_NAME;
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
        $table_name = MC2_Withdrawal::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE '.MC2_Withdrawal::DB_CREATOR_ID.'='.$idCreator;
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
        $table_name = MC2_Withdrawal::TABLE_NAME;
        $query      = 'SELECT sum(paid_init) as total_withdrawn FROM '.$table_name.' WHERE creator_id > 0';
        if( !empty( $idCreator ) ) $query .= ' AND creator_id = "'.trim( $idCreator ).'"';
        $result = $wpdb->get_results( $query )[0]->total_withdrawn;

        return number_format( $result, 2, '.', '' );
    }

}