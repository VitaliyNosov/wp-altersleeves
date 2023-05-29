<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Tax_Object;
use WP_Error;
use WP_Term;

/**
 * Class MC_Mtg_Set
 *
 * @package Mythic_Core\Objects\Set
 */
class MC_Mtg_Set extends MC_Tax_Object {
    
    public function __construct( $card = null ) {
        parent::__construct( $card );
        
        $this->setLink( '/browse?browse_type=sets&set_id='.$this->id );
    }
    
    /**
     * @param mixed $link
     */
    public function setLink( $link ) {
        $this->link = $link;
    }
    
    /**
     * @return string
     */
    public function getTaxCode() : string {
        return 'mtg_set';
    }
    
    /**
     * @return array|false|WP_Error|WP_Term|null
     */
    public static function unavailable() {
        $unavailable = get_term_by( 'name', 'Unavailable', 'mtg_set' );
        if( !empty( $unavailable ) ) return $unavailable;
        wp_insert_term( 'Unavailable', 'mtg_set' );
        
        return get_term_by( 'name', 'Unavailable', 'mtg_set' );
    }
    
    /**
     * @return int
     */
    public static function unavailableId() : int {
        return self::unavailable()->term_id;
    }
    
    /**
     * @return array|false|WP_Error|WP_Term|null
     */
    public static function available() {
        $available = get_term_by( 'name', 'Available', 'mtg_set' );
        if( !empty( $available ) ) return $available;
        wp_insert_term( 'Available', 'mtg_set' );
        
        return get_term_by( 'name', 'Available', 'mtg_set' );
    }
    
    /**
     * @return int
     */
    public static function availableId() : int {
        return self::available()->term_id;
    }
    
}
