<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Onsite_Object;

/**
 * Class MC_Withdrawal
 *
 * @package Mythic_Core\Objects
 */
class MC_Withdrawal extends MC_Onsite_Object {
    
    const TABLE_NAME        = 'mc_withdrawals';
    const DB_ID             = 'id';
    const DB_TRANSACTION_ID = 'transaction_id';
    const DB_CREATOR_ID     = 'creator_id';
    const DB_PAID_INIT      = 'paid_init';
    const DB_CURRENCY       = 'currency';
    const DB_SOURCE         = 'source';
    const DB_DATE           = 'date';
    const DB_EXPORTED       = 'exported';
    const DB_APPROVED       = 'approved';
    protected $idTransaction;
    protected $idCreator;
    protected $paidInit;
    protected $currency;
    protected $source;
    protected $date;
    protected $exported;
    protected $approved;
    
    /**
     * @param int $id
     *
     * @return array|mixed
     */
    public function load( $id = 0 ) {
        if( empty( $id ) ) return [];
        global $wpdb;
        $table_name = self::TABLE_NAME;
        $query      = "SELECT * FROM $table_name WHERE ".self::DB_ID." = $id";
        $results    = $wpdb->get_results( $query, ARRAY_A );
        if( $results == null ) return [];
        $result = $results[0];
        $load   = $this->initSetters( $result );
        if( !$load ) return [];
        
        return $results[0];
    }
    
    /**
     * @param $row
     *
     * @return bool
     */
    public function initSetters( $row = [] ) {
        if( !is_array( $row ) || empty( $row ) ) return false;
        if( !isset( $row[ self::DB_ID ] ) ||
            !isset( $row[ self::DB_TRANSACTION_ID ] ) ||
            !isset( $row[ self::DB_PAID_INIT ] ) ||
            !isset( $row[ self::DB_CURRENCY ] ) ||
            !isset( $row[ self::DB_DATE ] ) ||
            !isset( $row[ self::DB_SOURCE ] )
        ) {
            return false;
        }
        $this->id            = $row[ self::DB_ID ];
        $this->idTransaction = $row[ self::DB_TRANSACTION_ID ];
        $this->idCreator     = isset( $row[ self::DB_CREATOR_ID ] ) ? $row[ self::DB_CREATOR_ID ] : '';
        $this->paidInit      = $row[ self::DB_PAID_INIT ];
        $this->currency      = $row[ self::DB_CURRENCY ];
        $this->date          = $row[ self::DB_DATE ];
        $this->source        = $row[ self::DB_SOURCE ];
        $this->exported      = $row[ self::DB_EXPORTED ];
        $this->approved      = $row[ self::DB_APPROVED ];
        
        return true;
    }
    
    /**
     * @return string
     */
    public function tableQuery() : string {
        return "CREATE TABLE `mc_withdrawals` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `transaction_id` varchar(255) NOT NULL DEFAULT '',
                `creator_id` int(11) NOT NULL DEFAULT 0,
                `paid_init` float(20,2) NOT NULL,
                `currency` varchar(10) NOT NULL DEFAULT 'USD',
                `date` datetime NOT NULL,
                `source` varchar(255) NOT NULL DEFAULT 'transferwise',
                `exported` int(11) DEFAULT 1,
                `approved` int(1) DEFAULT 0,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=464 DEFAULT CHARSET=utf8;";
    }
    
    /**
     * @return bool|false|int
     */
    public function create() {
        global $wpdb;
        
        $idTransaction = time();
        $exported      = !empty( $this->getExported() ) ? $idTransaction : 0;
        $idCreator     = !empty( $this->getIdCreator() ) ? $this->getIdCreator() : 0;
        $paidInit      = $this->getPaidInit();
        $currency      = $this->getCurrency();
        $date          = str_replace( '/', '-', $this->getDate() );
        $source        = $this->getSource();
        
        $table_name = self::TABLE_NAME;
        
        $insert = $wpdb->insert( $table_name,
                                 [
                                     'transaction_id' => $idTransaction,
                                     'creator_id'     => $idCreator,
                                     'paid_init'      => $paidInit,
                                     'currency'       => $currency,
                                     'source'         => $source,
                                     'date'           => $date,
                                     'exported'       => $exported,
                                     'approved'       => $this->approved ?? 0
                                 ] );
        
        if( empty( $insert ) ) return false;
        
        $user = get_user_by( 'ID', $idCreator );
        if( empty( $user ) || $source != 'withdrawal' ) return $insert;
        
        ob_start(); ?>
        <p>Hello <?= $user->first_name ?>,</p>

        <p>You are receiving this because you have successfully requested a withdrawal of your available <a href="https:///www.altersleeves.com">Alter
                Sleeves</a> funds.</p>

        <p>The amount requested is <?= $paidInit ?> to be paid in <strong><?= $currency ?></strong>. Please email support@altersleeves.com if this is
            incorrect.</p>

        <p>Withdrawals are processed manually so may take up to 3 working days; thanks for your patience.</p>

        <strong>Team Alter Sleeves</strong><br>
        <img src="<?= DIR_THEME_IMAGES.'/logo/giftcard.png'; ?>">
        <?php
        $output = ob_get_clean();
        wp_mail( $user->user_email, 'Withdrawal Request Received', $output );
        
        return $insert;
    }
    
    /**
     * @return int
     */
    public function getIdCreator() {
        return $this->idCreator;
    }
    
    /**
     * @param int $idCreator
     */
    public function setIdCreator( $idCreator ) {
        $this->idCreator = $idCreator;
    }
    
    /**
     * @return float
     */
    public function getPaidInit() {
        return $this->paidInit;
    }
    
    /**
     * @param float $paidInit
     */
    public function setPaidInit( $paidInit ) {
        $this->paidInit = $paidInit;
    }
    
    /**
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }
    
    /**
     * @param string $currency
     */
    public function setCurrency( $currency ) {
        $this->currency = $currency;
    }
    
    /**
     * @return string
     */
    public function getDate() {
        return $this->date;
    }
    
    /**
     * @param string $date
     */
    public function setDate( $date ) {
        $this->date = $date;
    }
    
    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }
    
    /**
     * @param string $source
     */
    public function setSource( $source ) {
        $this->source = $source;
    }
    
    /**
     * @return mixed
     */
    public function getTransactionId() {
        return $this->idTransaction;
    }
    
    /**
     * @param mixed $idTransaction
     */
    public function setTransactionId( $idTransaction ) : void {
        $this->idTransaction = $idTransaction;
    }
    
    /**
     * @return mixed
     */
    public function getExported() {
        return $this->exported;
    }
    
    /**
     * @param mixed $exported
     */
    public function setExported( $exported ) : void {
        $this->exported = $exported;
    }
    
    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getApproved() {
        return $this->approved;
    }
    
    /**
     * @param mixed $approved
     */
    public function setApproved( $approved ) {
        $this->approved = $approved;
    }
    
}