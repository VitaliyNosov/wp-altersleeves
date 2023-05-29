<?php

namespace Mythic\Objects\Store\Products;

use Mythic\Objects\Store\MC2_Product;

class MC2_Collection extends MC2_Product {
    
    protected $designs;
    protected $products;
    
    public function __construct( $id = 0 ) {
        parent::__construct( $id );
    }
    
    /**
     * @return mixed
     */
    public function get_designs() {
        return $this->designs;
    }
    
    /**
     * @param mixed $designs
     */
    public function set_designs( $designs ) : void {
        $this->designs = $designs;
    }
    
    /**
     * @return mixed
     */
    public function get_products() {
        return $this->products;
    }
    
    /**
     * @param mixed $products
     */
    public function set_products( $products ) : void {
        $this->products = $products;
    }
    
}