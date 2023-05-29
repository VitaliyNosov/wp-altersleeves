<?php

namespace Mythic_Core\Abstracts;

/**
 * Class MC_Tax_Object
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Tax_Object {
    
    public $description = '';
    public $id = 0;
    public $meta = [];
    public $name = '';
    public $parent = 0;
    public $slug = '';
    public $tax;
    public $term_id = 0;
    
    /**
     * MC_Tax_Object constructor.
     *
     * @param null $term
     */
    public function __construct( $term = null ) {
        if( empty( $term ) ) return null;
        switch( $term ) {
            case is_numeric( $term ) :
                $tax = get_term_by( 'term_id', $term, $this->getTaxCode() );
                if( !is_object( $tax ) || empty( $tax ) ) return null;
                break;
            case is_string( $term ) :
                $tax = get_term_by( 'name', $term, $this->getTaxCode() );
                if( !is_object( $tax ) || empty( $tax ) ) return null;
                break;
            case is_object( $term ) :
                $tax = $term;
                break;
            default :
                return null;
        }
        $id   = $tax->term_id;
        $name = $tax->name;
        $meta = get_term_meta( $id ) ?? [];
        
        $this->setId( $id );
        $this->setName( $name );
        $this->setSlug( $tax->slug );
        $this->setDescription( $tax->description );
        $this->setMeta( $meta );
        $this->setParent( $tax->parent );
        $this->setTax( $tax );
        $this->setTermId( $id );
    }
    
    /**
     * @return string
     */
    abstract public function getTaxCode() : string;
    
    /**
     * @return string
     */
    public function getDescription() : string {
        return $this->description;
    }
    
    /**
     * @param string $description
     */
    public function setDescription( string $description ) {
        $this->description = $description;
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
    public function getMeta() : array {
        return $this->meta;
    }
    
    /**
     * @param array $meta
     */
    public function setMeta( array $meta ) {
        $this->meta = $meta;
    }
    
    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName( string $name ) {
        $this->name = $name;
    }
    
    /**
     * @return int
     */
    public function getParent() : int {
        return $this->parent;
    }
    
    /**
     * @param int $parent
     */
    public function setParent( int $parent ) {
        $this->parent = $parent;
    }
    
    /**
     * @return string
     */
    public function getSlug() : string {
        return $this->slug;
    }
    
    /**
     * @param string $slug
     */
    public function setSlug( string $slug ) {
        $this->slug = $slug;
    }
    
    /**
     * @return object
     */
    public function getTax() {
        return $this->tax;
    }
    
    /**
     * @param object $tax
     */
    public function setTax( object $tax ) {
        $this->tax = $tax;
    }
    
    /**
     * @return int
     */
    public function getTermId() : int {
        return $this->term_id;
    }
    
    /**
     * @param int $term_id
     */
    public function setTermId( int $term_id ) {
        $this->term_id = $term_id;
    }
    
}