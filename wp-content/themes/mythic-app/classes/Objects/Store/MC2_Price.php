<?php

namespace Mythic\Objects\Store;

use Mythic\Abstracts\MC2_Object;

class MC2_Price extends MC2_Object {
    
    protected static $table_name = 'prices';
    
    protected $name;
    protected $usd;
    protected $eur;
    protected $discount_id;
    
    /**
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function set_name( $name ) {
        $this->name = $name;
    }
    
    /**
     * @return float
     */
    public function get_usd() : float {
        return $this->usd;
    }
    
    /**
     * @param float $usd
     */
    public function set_usd( float $usd ) {
        $this->usd = $usd;
    }
    
    /**
     * @return float
     */
    public function get_eur() : float {
        return $this->eur;
    }
    
    /**
     * @param float $eur
     */
    public function set_eur( float $eur ) {
        $this->eur = $eur;
    }
    
    /**
     * @return int
     */
    public function get_discount_id() : int {
        return $this->discount_id;
    }
    
    /**
     * @param int $discount_id
     */
    public function set_discount_id( int $discount_id ) {
        $this->discount_id = $discount_id;
    }
    
}