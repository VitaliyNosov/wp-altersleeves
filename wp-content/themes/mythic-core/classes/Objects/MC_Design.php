<?php

namespace Mythic_Core\Objects;

/**
 * Class Design
 *
 * @package Mythic_Core\Objects
 */
class MC_Design {
    
    const META_ALTERS         = 'mc_linked_variations';
    const META_LINKED_DESIGNS = 'mc_connected_designs';
    
    public $id = 0;
    public $alters = [];
    public $linkedDesigns = [];
    
    /**
     * Design constructor.
     *
     * @param null $id
     */
    public function __construct( $id = null ) {
        if( empty( $id ) ) return;
        $meta = get_post_meta( $id );
        $this->setId( $id );
        $this->setAlters( $meta[ self::META_ALTERS ] );
        // Design Type to be pulled from tax not meta
        $this->setLinkedDesigns( $meta[ self::META_LINKED_DESIGNS ] ?? [] );
    }
    
    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId( int $id ) {
        $this->id = $id;
    }
    
    /**
     * @return array
     */
    public function getAlters() : array {
        return $this->alters;
    }
    
    /**
     * @param array $alters
     */
    public function setAlters( array $alters ) {
        $this->alters = $alters;
    }
    
    /**
     * @return array
     */
    public function getLinkedDesigns() : array {
        return $this->linkedDesigns;
    }
    
    /**
     * @param array $linkedDesigns
     */
    public function setLinkedDesigns( array $linkedDesigns ) {
        $this->linkedDesigns = $linkedDesigns;
    }
    
}

