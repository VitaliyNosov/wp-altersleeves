<?php

namespace Mythic\Objects\Store\Product;

use Mythic\Abstracts\MC2_Object;

class MC2_Gift_Card_Code extends MC2_Object {
    
    protected static $table_name = 'gift_card_codes';
    protected $code;
    protected $visible_id;
    
    /**
     * @return string
     */
    public function get_code() : string {
        return $this->code;
    }
    
    /**
     * @param string $code
     */
    public function set_code( string $code ) {
        $this->code = $code;
    }
    
    /**
     * @return int
     */
    public function get_visible_id() : int {
        return $this->visible_id;
    }
    
    /**
     * @param int $visible_id
     */
    public function set_visible_id( int $visible_id ) {
        $this->visible_id = $visible_id;
    }
    
    /**
     * @return array
     */
    public function get_object_data() : array {
        $id   = $this->get_id();
        $code = $this->get_code();
        
        if( empty( $id ) ) {
            $result = $this->get_id_where( [ 'code' => $code ] );
            $id     = is_numeric( $result ) && !empty( $result ) ? $result : $id;
            $this->set_id( $id );
        }
        
        return [
            'id'         => $id,
            'code'       => $code,
            'visible_id' => $this->get_visible_id()
        ];
    }
    
}