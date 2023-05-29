<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Tax_Object;

/**
 * Class MC_Mtg_Card
 *
 * @package Mythic_Core\Objects
 */
class MC_Mtg_Card extends MC_Tax_Object {
    
    public $brand = 'mtg';
    public $image = '';
    public $images = '';
    public $creations = 0;
    public $nice_name = '';
    public $link;
    
    public function __construct( $card = null ) {
        parent::__construct( $card );
        
        $meta = $this->meta;
        $this->setNiceName( $meta['mc_searchable_name'][0] ?? '' );
        $this->setLink( '/browse?browse_type=cards&card_id='.$this->id );
        $browsing_data = $this->meta['mc_design_results'][0] ?? [];
        $creations     = $browsing_data['total'] ?? 0;
        $this->setCreations( $creations );
    }
    
    /**
     * @return string
     */
    public function getBrand() : string {
        return $this->brand;
    }
    
    /**
     * @param string $brand
     */
    public function setBrand( string $brand ) : void {
        $this->brand = $brand;
    }
    
    /**
     * @return int
     */
    public function getCreations() : int {
        return $this->creations;
    }
    
    /**
     * @param int $creations
     */
    public function setCreations( int $creations ) : void {
        $this->creations = $creations;
    }
    
    /**
     * @return string
     */
    public function getImage() : string {
        return $this->image;
    }
    
    /**
     * @param string $image
     */
    public function setImage( string $image ) : void {
        $this->image = $image;
    }
    
    /**
     * @return string
     */
    public function getImages() : string {
        return $this->images;
    }
    
    /**
     * @param string $images
     */
    public function setImages( string $images ) : void {
        $this->images = $images;
    }
    
    /**
     * @return mixed
     */
    public function getLink() {
        return $this->link;
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
    public function getNiceName() : string {
        return $this->nice_name;
    }
    
    /**
     * @param string $nice_name
     */
    public function setNiceName( string $nice_name ) : void {
        $this->nice_name = $nice_name;
    }
    
    /**
     * @return string
     */
    public function getTaxCode() : string {
        return $this->brand.'_card';
    }
    
}