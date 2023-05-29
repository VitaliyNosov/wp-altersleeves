<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Post_Type_Object;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;
use Mythic_Core\System\MC_WP;

/**
 * Class MC_Alter
 *
 * @package Mythic_Core\Objects
 */
class MC_Alter extends MC_Post_Type_Object {
    
    public const META_ADDITIONAL_PRINTINGS   = 'mc_additional_printings';
    public const META_LINKED_DESIGN          = 'mc_linked_design';
    public const META_LINKED_PRINTING        = 'mc_linked_printing';
    public const META_VIOLATION_TEXT         = 'mc_violation_text';
    public const META_FILE_ORIGINAL          = 'mc_original_alter';
    public const META_FILE_COMBINED_MINI_JPG = 'mc_mini_res_combined_jpg';
    public const META_FILE_COMBINED_LO_JPG   = 'mc_lo_res_combined_jpg';
    public const META_FILE_COMBINED_HI_JPG   = 'mc_hi_res_combined_jpg';
    public const META_FILE_COMBINED_LO_PNG   = 'mc_lo_res_combined_png';
    public const META_FILE_COMBINED_HI_PNG   = 'mc_hi_res_combined_png';
    public const META_FILE_ALTER_LO_PNG      = 'mc_lo_res_alter_png';
    public const META_FILE_ALTER_HI_PNG      = 'mc_hi_res_alter_png';
    public const META_FILE_SOCIAL_SQUARE     = 'mc_social_square';
    
    public $id = 0;
    public $artist_id = 0;
    public $land_frame = '';
    public $additionalPrintings = [];
    public $linkedDesign = '';
    public $linkedPrinting = 0;
    public $linkedPrintings = [];
    public $violationText = '';
    public $fileOriginal = '';
    public $fileCombinedJpgMini = '';
    public $fileCombinedJpgLo = '';
    public $fileCombinedJpgHi = '';
    public $fileCombinedPngLo = '';
    public $fileCombinedPngHi = '';
    public $filePngLo = '';
    public $filePngHi = '';
    public $fileSocialSquare = '';
    
    /**
     * Alter constructor.
     */
    public function __construct( $id = null ) {
        parent::__construct( $id );
        if( empty( $id ) ) return;
        $meta        = get_post_meta( $id );
        $printing_id = $meta[ self::META_LINKED_PRINTING ][0] ?? 0;
        $printings   = $meta[ self::META_ADDITIONAL_PRINTINGS ][0] ?? [];
        if( empty( $printings ) ) $printings = [];
        if( is_string( $printings ) ) $printings = maybe_unserialize( $printings );
        if( empty( $printing_id ) && empty( $printings ) ) return;
        if( empty( $printing_id ) ) $printing_id = $printings[0];
        if( empty( $printings ) ) $printings = [ $printing_id ];
        
        $this->setId( $id );
        $artist_id = MC_WP::authorId( $id );
        $this->setArtistId( $artist_id );
        $this->setLinkedDesign( $meta[ self::META_LINKED_DESIGN ][0] ?? 0 );
        $this->setLinkedPrinting( $printing_id );
        $this->setAdditionalPrintings( $printings );
        $this->setViolationText( self::META_VIOLATION_TEXT );
        $this->setFileOriginal( ABSPATH.'/files/prints/png'.$id.'.png' );
        $this->setFileCombinedJpgMini( $meta[ self::META_FILE_COMBINED_MINI_JPG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg() );
        $this->setFileCombinedJpgLo( $meta[ self::META_FILE_COMBINED_LO_JPG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg() );
        $this->setFileCombinedJpgHi( $meta[ self::META_FILE_COMBINED_HI_JPG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg() );
        $this->setFileCombinedPngLo( $meta[ self::META_FILE_COMBINED_LO_PNG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg( 'png' ) );
        $this->setFileCombinedPngHi( $meta[ self::META_FILE_COMBINED_HI_PNG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg( 'png' ) );
        $this->setFilePngHi( $meta[ self::META_FILE_ALTER_HI_PNG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg( 'png' ) );
        $this->setFilePngLo( $meta[ self::META_FILE_ALTER_LO_PNG ][0] ?? MC_Mtg_Card_Functions::unavailableCardImg( 'png' ) );
        $this->setFileSocialSquare( $meta[ self::META_FILE_SOCIAL_SQUARE ][0] ?? '' );
    }
    
    /**
     * @return int
     */
    public function getArtistId() : int {
        return $this->artist_id;
    }
    
    /**
     * @param int $artist_id
     */
    public function setArtistId( int $artist_id ) : void {
        $this->artist_id = $artist_id;
    }
    
    /**
     * @return int
     */
    public function getLinkedPrinting() : int {
        return $this->linkedPrinting;
    }
    
    /**
     * @param int $linkedPrinting
     */
    public function setLinkedPrinting( int $linkedPrinting ) {
        $this->linkedPrinting = $linkedPrinting;
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
    public function getAdditionalPrintings() : array {
        return $this->additionalPrintings;
    }
    
    /**
     * @param array $additionalPrintings
     */
    public function setAdditionalPrintings( array $additionalPrintings ) {
        $this->additionalPrintings = $additionalPrintings;
    }
    
    /**
     * @return string
     */
    public function getLinkedDesign() : string {
        return $this->linkedDesign;
    }
    
    /**
     * @param string $linkedDesign
     */
    public function setLinkedDesign( string $linkedDesign ) {
        $this->linkedDesign = $linkedDesign;
    }
    
    /**
     * @return array
     */
    public function getLinkedPrintings() : array {
        return $this->linkedPrintings;
    }
    
    /**
     * @param array $linkedPrintings
     */
    public function setLinkedPrintings( array $linkedPrintings ) {
        $this->linkedPrintings = $linkedPrintings;
    }
    
    /**
     * @return string
     */
    public function getViolationText() : string {
        return $this->violationText;
    }
    
    /**
     * @param string $violationText
     */
    public function setViolationText( string $violationText ) {
        $this->violationText = $violationText;
    }
    
    /**
     * @return string
     */
    public function getFileOriginal() : string {
        return $this->fileOriginal;
    }
    
    /**
     * @param string $fileOriginal
     */
    public function setFileOriginal( string $fileOriginal ) {
        $this->fileOriginal = $fileOriginal;
    }
    
    /**
     * @return string
     */
    public function getFileCombinedJpgMini() : string {
        return $this->fileCombinedJpgMini;
    }
    
    /**
     * @param string $fileCombinedJpgMini
     */
    public function setFileCombinedJpgMini( string $fileCombinedJpgMini ) {
        $this->fileCombinedJpgMini = $fileCombinedJpgMini;
    }
    
    /**
     * @return string
     */
    public function getFileCombinedJpgLo() : string {
        return $this->fileCombinedJpgLo;
    }
    
    /**
     * @param string $fileCombinedJpgLo
     */
    public function setFileCombinedJpgLo( string $fileCombinedJpgLo ) {
        $this->fileCombinedJpgLo = $fileCombinedJpgLo;
    }
    
    /**
     * @return string
     */
    public function getFileCombinedJpgHi() : string {
        return $this->fileCombinedJpgHi;
    }
    
    /**
     * @param string $fileCombinedJpgHi
     */
    public function setFileCombinedJpgHi( string $fileCombinedJpgHi ) {
        $this->fileCombinedJpgHi = $fileCombinedJpgHi;
    }
    
    /**
     * @return string
     */
    public function getFileCombinedPngLo() : string {
        return $this->fileCombinedPngLo;
    }
    
    /**
     * @param string $fileCombinedPngLo
     */
    public function setFileCombinedPngLo( string $fileCombinedPngLo ) {
        $this->fileCombinedPngLo = $fileCombinedPngLo;
    }
    
    /**
     * @return string
     */
    public function getFileCombinedPngHi() : string {
        return $this->fileCombinedPngHi;
    }
    
    /**
     * @param string $fileCombinedPngHi
     */
    public function setFileCombinedPngHi( string $fileCombinedPngHi ) {
        $this->fileCombinedPngHi = $fileCombinedPngHi;
    }
    
    /**
     * @return string
     */
    public function getFileSocialSquare() : string {
        return $this->fileSocialSquare;
    }
    
    /**
     * @param string $fileSocialSquare
     */
    public function setFileSocialSquare( string $fileSocialSquare ) {
        $this->fileSocialSquare = $fileSocialSquare;
    }
    
    /**
     * @return string
     */
    public function getFilePngLo() : string {
        return $this->filePngLo;
    }
    
    /**
     * @param string $filePngLo
     */
    public function setFilePngLo( string $filePngLo ) {
        $this->filePngLo = $filePngLo;
    }
    
    /**
     * @return string
     */
    public function getFilePngHi() : string {
        return $this->filePngHi;
    }
    
    /**
     * @param string $filePngHi
     */
    public function setFilePngHi( string $filePngHi ) {
        $this->filePngHi = $filePngHi;
    }
    
}