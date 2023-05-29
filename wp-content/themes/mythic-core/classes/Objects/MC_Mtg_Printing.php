<?php

namespace Mythic_Core\Objects;

use MC_Mtg_Printing_Functions;
use Mythic_Core\Abstracts\MC_Post_Type_Object;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Utils\MC_Scryfall;

/**
 * Class MC_Mtg_Printing
 *
 * @package Mythic_Core\Objects
 */
class MC_Mtg_Printing extends MC_Post_Type_Object {

    public const META_SET_NAME_                = 'mc_set_name';
    public const META_SET_CODE         = 'mc_set_code';
    
    public $id = 0;
    public $name = '';
    public $set_code = '';
    public $set_name = '';
    public $set_id = '';
    public $set = '';
    public $collector_number = null;
    public $language = '';
    public $edhrec_rank = 0;
    public $artist = '';
    public $type_line = '';
    public $layout = '';
    public $frame = '';
    public $frame_effect = '';
    public $foil = 0;
    public $fullName = '';
    public $border_color = '';
    public $rarity = '';
    public $imgJpgSmall = '';
    public $image = '';
    public $imgJpgNormal = '';
    public $imgJpgNormalSide1 = '';
    public $imgJpgLg = '';
    public $imgPng = '';
    public $imgPngSide1 = '';
    public $framecode_id = 0;
    public $framecode;
    public $card_id = 0;
    public $card_creations = 0;
    public $creations = 0;
    public $scryfall_id;

    public function __construct( $term ) {
        parent::__construct( $term );
        $meta        = $this->getMeta();
        $scryfall_id = $meta['mc_scryfall_id'] ?? $meta['mc_id'] ?? '';
        if( empty( $scryfall_id ) ) {
            foreach ( ['mc_image_hi_res_jpg', 'mc_image_normal_jpg'] as $imageResolution ) {
                if ( !$meta || !isset($meta[$imageResolution]) ) continue;
                $file        = $meta[$imageResolution];
                $file        = pathinfo( $file );
                if ( !$file['filename'] ) continue;
                $scryfall_id = $file['filename'];
                $scryfall_id = str_replace( '-large', '', $scryfall_id );
                if( !empty($scryfall_id) ) break;
            }
        }
        if( empty( $scryfall_id ) ) return;
        $this->setScryfallId( $scryfall_id );
        $card = MC_Scryfall::get_card_by_scryfall_id( $scryfall_id );
        
        if( empty( $card ) ) return;
        
        $name             = $card->name;
        $collector_number = $card->collector_number;
        $this->setName( $name );
        $this->setCollectorNumber( $collector_number );
        $framecode = MC_Mtg_Printing_Functions::getFramecode( $this->id );
        $this->setFrameCode( $framecode );
        $this->setFrameCodeId( $framecode->term_id ?? 0 );
        
        $this->setSetCode( $card->set );
        $this->setSetName( $card->set_name );
        $this->setFullName( $card->set_name.' ('.$collector_number.')' );
        
        $this->setLanguage( $card->lang );
        $this->setTypeLine( $card->type_line );
        
        $this->setFrameEffect( $card->frame_effects );
        $this->setBorderColor( $card->border_color );
        $this->setEdhrecRank( $card->edhrec_rank );
        
        $image_normal = $card->jpg_normal ?? $meta['mc_image_normal_jpg'];
        $image_normal = substr( $image_normal, 0, 1 ) == 'h' ? $image_normal : MC_SITE.$image_normal;
        $this->setImgJpgNormal( $image_normal );
        $this->setImage( $image_normal );
        $this->setImgJpgNormalSide1( $image_normal );
        $image_large = $card->jpg_large ?? $meta['mc_image_hi_res_jpg'];
        $image_large = substr( $image_large, 0, 1 ) == 'h' ? $image_large : MC_SITE.$image_large;
        $this->setImgJpgLg( $image_large );
        $image_png = $card->png ?? $meta['mc_image_png'] ?? $meta['mc_image_side_1_png'];
        $image_png = substr( $image_png, 0, 1 ) == 'h' ? $image_png : MC_SITE.$image_png;
        $this->setImgPng( $image_png );
        $this->setImgPngSide1( $image_png );
    }
    
    /**
     * @param mixed $scryfall_id
     */
    public function setScryfallId( $scryfall_id ) : void {
        $this->scryfall_id = $scryfall_id;
    }
    
    /**
     * @param mixed $meta
     */
    public function setMeta() {
        $meta = get_post_meta( $this->id );
        foreach( $meta as $key => $meta_item ) {
            if( !is_array($meta_item) ) continue;
            $meta[$key] = $meta_item[0];
        }
        $this->meta = $meta;
    }
    
    /**
     * @return mixed
     */
    public function getMeta() {
        return $this->meta;
    }
    
    /**
     * @param int $card_creations
     */
    public function setCardCreations( int $card_creations ) {
        $this->card_creations = $card_creations;
    }
    
    /**
     * @return mixed
     */
    public function getCollectorNumber() {
        return $this->collector_number;
    }
    
    /**
     * @param mixed $collector_number
     */
    public function setCollectorNumber( $collector_number ) {
        $this->collector_number = $collector_number;
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
    public function setImage( string $image ) {
        $this->image = $image;
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
    public function setCreations( int $creations ) {
        $this->creations = $creations;
    }
    
    /**
     * @return int
     */
    public function getEdhrecRank() : int {
        return $this->edhrec_rank;
    }
    
    /**
     * @param int $edhrec_rank
     */
    public function setEdhrecRank( int $edhrec_rank ) {
        $this->edhrec_rank = $edhrec_rank;
    }
    
    /**
     * @param int $framecode_id
     */
    public function setFrameCodeId( int $framecode_id ) {
        $this->framecode_id = $framecode_id;
    }
    
    /**
     * @return object
     */
    public function getFrameCode() : string {
        return $this->framecode;
    }
    
    /**
     * @param object $framecode
     */
    public function setFrameCode( $framecode ) {
        $this->framecode = $framecode;
    }
    
    /**
     * @return int
     */
    public function getCardId() : int {
        return $this->card_id;
    }
    
    /**
     * @param int $card_id
     */
    public function setCardId( int $card_id ) {
        $this->card_id = $card_id;
    }
    
    /**
     * @return string
     */
    public function getSetCode() : string {
        return $this->set_code;
    }
    
    /**
     * @param string $setCode
     */
    public function setSetCode( $setCode ) {
        $this->set_code = $setCode;
    }
    
    /**
     * @return string
     */
    public function getSetName() : string {
        return $this->set_name;
    }
    
    /**
     * @param string $set_name
     */
    public function setSetName( string $set_name ) {
        $this->set_name = $set_name;
    }
    
    /**
     * @return int
     */
    public function getCollectorsNumber() : int {
        return $this->collector_number;
    }
    
    /**
     * @param mixed $collectorsNumber
     */
    public function setCollectorsNumber( $collectorsNumber ) {
        $this->collector_number = $collectorsNumber;
    }
    
    /**
     * @return string
     */
    public function getLanguage() : string {
        return $this->language;
    }
    
    /**
     * @param string $language
     */
    public function setLanguage( string $language ) {
        $this->language = $language;
    }
    
    /**
     * @return string
     */
    public function getArtist() : string {
        return $this->artist;
    }
    
    /**
     * @param string $artist
     */
    public function setArtist( string $artist ) {
        $this->artist = $artist;
    }
    
    /**
     * @return string
     */
    public function getTypeLine() : string {
        return $this->type_line;
    }
    
    /**
     * @param string $type_line
     */
    public function setTypeLine( string $type_line ) {
        $this->type_line = $type_line;
    }
    
    /**
     * @return string
     */
    public function getLayout() : string {
        return $this->layout;
    }
    
    /**
     * @param string $layout
     */
    public function setLayout( string $layout ) {
        $this->layout = $layout;
    }
    
    /**
     * @return string
     */
    public function getFrame() : string {
        return $this->frame;
    }
    
    /**
     * @param string $frame
     */
    public function setFrame( string $frame ) {
        $this->frame = $frame;
    }
    
    /**
     * @param string $frame_effect
     */
    public function setFrameEffect( string $frame_effect ) {
        $this->frame_effect = $frame_effect;
    }
    
    /**
     * @return int
     */
    public function getFoil() : int {
        return $this->foil;
    }
    
    /**
     * @param int $foil
     */
    public function setFoil( int $foil ) {
        $this->foil = $foil;
    }
    
    /**
     * @return string
     */
    public function getFullName() : string {
        return $this->fullName;
    }
    
    /**
     * @param string $fullName
     */
    public function setFullName( string $fullName ) {
        $this->fullName = $fullName;
    }
    
    /**
     * @return string
     */
    public function getBorderColor() : string {
        return $this->border_color;
    }
    
    /**
     * @param string $border_color
     */
    public function setBorderColor( string $border_color ) {
        $this->border_color = $border_color;
    }
    
    /**
     * @return string
     */
    public function getRarity() : string {
        return $this->rarity;
    }
    
    /**
     * @param string $rarity
     */
    public function setRarity( string $rarity ) {
        $this->rarity = $rarity;
    }
    
    /**
     * @return string
     */
    public function getImgJpgSmall() : string {
        return $this->imgJpgSmall;
    }
    
    /**
     * @param string $imgJpgSmall
     */
    public function setImgJpgSmall( string $imgJpgSmall ) {
        $this->imgJpgSmall = $imgJpgSmall;
    }
    
    /**
     * @return string
     */
    public function getImgJpgNormal() : string {
        return $this->imgJpgNormal;
    }
    
    /**
     * @param string $imgJpgNormal
     */
    public function setImgJpgNormal( string $imgJpgNormal ) {
        $this->imgJpgNormal = $imgJpgNormal;
    }
    
    /**
     * @return string
     */
    public function getImgJpgNormalSide1() : string {
        return $this->imgJpgNormalSide1;
    }
    
    /**
     * @param string $imgJpgNormalSide1
     */
    public function setImgJpgNormalSide1( string $imgJpgNormalSide1 ) {
        $this->imgJpgNormalSide1 = $imgJpgNormalSide1;
    }
    
    /**
     * @return string
     */
    public function getImgJpgLg() : string {
        return $this->imgJpgLg;
    }
    
    /**
     * @param string $imgJpgLg
     */
    public function setImgJpgLg( string $imgJpgLg ) {
        $this->imgJpgLg = $imgJpgLg;
    }
    
    /**
     * @return string
     */
    public function getImgPng() : string {
        return $this->imgPng;
    }
    
    /**
     * @param string $imgPng
     */
    public function setImgPng( string $imgPng ) {
        $this->imgPng = $imgPng;
    }
    
    /**
     * @param string $imgPngSide1
     */
    public function setImgPngSide1( string $imgPngSide1 ) {
        $this->imgPngSide1 = $imgPngSide1;
    }
    
    public function getCard() {
        $card_id = MC_Mtg_Printing_Functions::getCardId( $this->getId(), $this->getName() );
        if( !empty( $card_id ) ) {
            $this->setCardId( $card_id );
            $card = new MC_Mtg_Card( $card_id );
            $this->setCardCreations( $card->creations );
        }
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
     * @return string
     */
    public function getSetId() : string {
        return $this->set_id;
    }
    
    /**
     * @param string $set_id
     */
    public function setSetId( string $set_id ) {
        $this->set_id = $set_id;
    }
    
    /**
     * @return string
     */
    public function getSet() : string {
        return $this->set;
    }
    
    /**
     * @param mixed $set
     */
    public function setSet( $set ) {
        $this->set = $set;
    }
    
}