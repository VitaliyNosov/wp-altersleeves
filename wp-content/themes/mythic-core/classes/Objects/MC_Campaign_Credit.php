<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Onsite_Object;

/**
 * Class MC_Campaign_Credit
 *
 * @package Mythic_Core\Objects
 */
class MC_Campaign_Credit extends MC_Onsite_Object {
    
    const TABLE_NAME = 'mc_campaign_credits';
    protected $user_id = 0;
    protected $quantity = 1;
    protected $campaign = 1;
    protected $campaign_group = 0;
    protected $credit_type = 'set';
    protected $product_type = 'set';
    protected $product_id = 0;
    
    public function __construct( $id = 0 ) {
        parent::__construct( $id );
        $result = $this->result ?? [];
        if( empty( $result ) ) return;
        $this->setUserId( $result->user_id ?? $this->user_id );
        $this->setQuantity( $result->quantity ?? $this->quantity );
        $this->setCampaign( $result->campaign ?? $this->campaign );
        $this->setCampaignGroup( $result->campaign_group ?? $this->campaign_group );
        $this->setCreditType( $result->credit_type ?? $this->credit_type );
        $this->setProductType( $result->product_type ?? $this->product_type );
        // @Todo add product id for commanders
    }
    
    public function save() {
    }
    
    /**
     * @return string
     */
    public function tableQuery() : string {
        return "CREATE TABLE `mc_campaign_credits` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL DEFAULT 0,
                  `quantity` int(11) NOT NULL DEFAULT 1,
                  `campaign` int(11) NOT NULL DEFAULT 1,
                  `campaign_group` int(11) NOT NULL,
                  `credit_type` varchar(255) NOT NULL DEFAULT 'set',
                  `product_type` varchar(255) NOT NULL DEFAULT 'set',
                  `product_id` varchar(255) DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    }
    
    /**
     * @return int
     */
    public function getUserId() : int {
        return $this->user_id;
    }
    
    /**
     * @param int $user_id
     */
    public function setUserId( int $user_id ) : void {
        $this->user_id = $user_id;
    }
    
    /**
     * @return int
     */
    public function getCampaign() : int {
        return $this->campaign;
    }
    
    /**
     * @param int $campaign
     */
    public function setCampaign( int $campaign ) : void {
        $this->campaign = $campaign;
    }
    
    /**
     * @return int
     */
    public function getQuantity() : int {
        return $this->quantity;
    }
    
    /**
     * @param int $quantity
     */
    public function setQuantity( int $quantity ) : void {
        $this->quantity = $quantity;
    }
    
    /**
     * @return int
     */
    public function getCampaignGroup() : int {
        return $this->campaign_group;
    }
    
    /**
     * @param int $campaign_group
     */
    public function setCampaignGroup( int $campaign_group ) : void {
        $this->campaign_group = $campaign_group;
    }
    
    /**
     * @return string
     */
    public function getCreditType() : string {
        return $this->credit_type;
    }
    
    /**
     * @param string $credit_type
     */
    public function setCreditType( string $credit_type ) : void {
        $this->credit_type = $credit_type;
    }
    
    /**
     * @return string
     */
    public function getProductType() : string {
        return $this->product_type;
    }
    
    /**
     * @param string $product_type
     */
    public function setProductType( string $product_type ) : void {
        $this->product_type = $product_type;
    }
    
    /**
     * @return int
     */
    public function getProductId() : int {
        return $this->product_id;
    }
    
    /**
     * @param int $product_id
     */
    public function setProductId( int $product_id ) : void {
        $this->product_id = $product_id;
    }
    
}