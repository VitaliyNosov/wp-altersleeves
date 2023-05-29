<?php

namespace Mythic_Core\Ajax\Marketing;

use Intervention\Image\ImageManagerStatic;
use MC_Alter_Functions;
use MC_Mtg_Printing;
use MC_Url;
use Mythic_Core\Abstracts\MC_Ajax;
use ZipArchive;

/**
 * Class ShortenLink
 *
 * @package Mythic_Core\Ajax\Marketing
 */
class MC_Get_Assets extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idAlter     = $_POST['product_id'];
        $printing_id = !empty( $_POST['printing_id'] ) ? $_POST['printing_id'] : MC_Alter_Functions::printing( $idAlter );
        $url         = '';
        $path        = ABSPATH.'files/ads';
        if( !is_dir( $path ) ) mkdir( $path, 0755, true );
        
        $zipPath = $path.'/'.$idAlter.'-'.$printing_id.'.zip';
        if( file_exists( $zipPath ) ) unlink( $zipPath );
        
        $templateImage = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/ad-template.png';
        $artbox_image  = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/artbox.png';
        $sleeveImage   = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/sleeve-layer.png';
        $sheenImage    = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/sheen-layer.png';
        $gleamImage    = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/gleam-layer.png';
        
        $width   = 630;
        $height  = 884;
        $offsetX = 8;
        $offsetY = 10;
        
        $printing  = new MC_Mtg_Printing( $printing_id );
        $imageCard = $printing->imgPng;
        $imageCard = MC_Url::urlToPath( $imageCard );
        $adCard    = $path.'/'.$printing_id.'.png';
        $cardImage = ImageManagerStatic::make( $imageCard );
        $cardImage->resize( $width, $height );
        $cardImage->save( $adCard );
        
        $imageAlter = MC_Alter_Functions::image( $idAlter, 'hi' );
        $imageAlter = MC_Url::urlToPath( $imageAlter );
        $adAlter    = $path.'/'.$idAlter.'.png';
        $alterImage = ImageManagerStatic::make( $imageAlter );
        $alterImage->resize( $width, $height );
        $alterImage->save( $adAlter );
        
        $reducedImage = ImageManagerStatic::make( $templateImage );
        $reducedImage->insert( $cardImage, 'top-left', $offsetX, $offsetY );
        $adReduced = $path.'/'.$printing_id.'-reduced.png';
        $reducedImage->save( $adReduced );
        
        $alterImage = ImageManagerStatic::make( $imageAlter );
        $alterImage->resize( $width, $height );
        $templateImage = ImageManagerStatic::make( $templateImage );
        $sleeveImage   = ImageManagerStatic::make( $sleeveImage );
        $sheenImage    = ImageManagerStatic::make( $sheenImage );
        $gleamImage    = ImageManagerStatic::make( $gleamImage );
        $templateImage->insert( $sleeveImage, 'top-left', 0, 0 );
        $templateImage->insert( $alterImage, 'top-left', $offsetX, $offsetY );
        $templateImage->insert( $sheenImage, 'top-left', 0, 0 );
        $templateImage->insert( $gleamImage, 'top-left', 0, 0 );
        $adAlterSleeve = $path.'/'.$idAlter.'-sleeve.png';
        $templateImage->save( $adAlterSleeve );
        
        $templateImage = ImageManagerStatic::make( $templateImage );
        $sleeveImage   = ImageManagerStatic::make( $sleeveImage );
        $sheenImage    = ImageManagerStatic::make( $sheenImage );
        $gleamImage    = ImageManagerStatic::make( $gleamImage );
        $templateImage->insert( $sleeveImage, 'top-left', 0, 0 );
        $templateImage->insert( $cardImage, 'top-left', $offsetX, $offsetY );
        $templateImage->insert( $alterImage, 'top-left', $offsetX, $offsetY );
        $templateImage->insert( $sheenImage, 'top-left', 0, 0 );
        $templateImage->insert( $gleamImage, 'top-left', 0, 0 );
        $adCombined = $path.'/'.$idAlter.'-'.$printing_id.'.png';
        $templateImage->save( $adCombined );
        
        $width       = $width * 2.2;
        $height      = $height * 2.2;
        $offsetX     = -150;
        $offsetY     = -250;
        $artboxImage = ImageManagerStatic::make( $artbox_image );
        $cardImage   = ImageManagerStatic::make( $imageCard );
        $cardImage->resize( $width, $height );
        $alterImage = ImageManagerStatic::make( $imageAlter );
        $alterImage->resize( $width, $height );
        $artboxImage->insert( $cardImage, 'top-left', $offsetX, $offsetY );
        $artboxImage->insert( $alterImage, 'top-left', $offsetX, $offsetY );
        $artboxImage->blur( 45 );
        $adArtbox = $path.'/'.$idAlter.'-'.$printing_id.'-artbox.png';
        $artboxImage->save( $adArtbox );
        
        $artboxCardImage = ImageManagerStatic::make( $artbox_image );
        $artboxCardImage->insert( $cardImage, 'top-left', $offsetX, $offsetY );
        $artboxCardImage->blur( 45 );
        $adArtboxCard = $path.'/'.$idAlter.'-'.$printing_id.'-artbox-card.png';
        $artboxCardImage->save( $adArtboxCard );
        
        $zip = new ZipArchive();
        $zip->open( $zipPath, ZipArchive::CREATE );
        $files  = [ $adCard, $adAlter, $adCombined, $adAlterSleeve, $adArtbox, $adArtboxCard, $adReduced ];
        $zipUrl = MC_Url::pathToUrl( $zipPath );
        foreach( $files as $file ) {
            $fileName = basename( $file );
            $zip->addFile( "{$file}", $fileName );
        }
        $zip->close();
        if( file_exists( $zipPath ) ) $url = $zipUrl;
        unlink( $adCard );
        unlink( $adAlter );
        unlink( $adCombined );
        unlink( $adAlterSleeve );
        unlink( $adArtbox );
        unlink( $adArtboxCard );
        
        $this->success( [ 'url' => $url ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'cas-generate-ad-assets';
    }
    
}